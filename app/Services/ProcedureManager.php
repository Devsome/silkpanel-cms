<?php

namespace App\Services;

use App\Actions\WebmallPurchaseAction;
use App\Contracts\ProcedurableAction;
use App\Enums\DatabaseNameEnums;
use App\Helpers\SettingHelper;
use App\Models\ProcedureLog;
use App\Models\ProcedureMapping;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Throwable;

class ProcedureManager
{
    /**
     * @return array<string, array{key: string, label: string, default_parameter_map: array<int, array{laravel_key: string, procedure_param: string, position: int}>}>
     */
    public function getActions(): array
    {
        $actions = [];

        foreach ($this->registeredActions() as $action) {
            $actions[$action->key()] = [
                'key' => $action->key(),
                'label' => $action->label(),
                'default_parameter_map' => $action->defaultParameterMap(),
            ];
        }

        return $actions;
    }

    public function ensureActionMappingsExist(): void
    {
        foreach ($this->getActions() as $action) {
            ProcedureMapping::query()->firstOrCreate(
                ['action' => $action['key']],
                [
                    'action_label' => $action['label'],
                    'database_connection' => DatabaseNameEnums::SRO_SHARD->value,
                    'parameter_map' => $action['default_parameter_map'],
                    'is_active' => false,
                    'use_fallback' => true,
                ]
            );
        }
    }

    public function getOrCreateMapping(string $actionKey): ProcedureMapping
    {
        $action = $this->getActions()[$actionKey] ?? null;

        if ($action === null) {
            throw new InvalidArgumentException("Unknown procedurable action: {$actionKey}");
        }

        return ProcedureMapping::query()->firstOrCreate(
            ['action' => $actionKey],
            [
                'action_label' => $action['label'],
                'database_connection' => DatabaseNameEnums::SRO_SHARD->value,
                'parameter_map' => $action['default_parameter_map'],
                'is_active' => false,
                'use_fallback' => true,
            ]
        );
    }

    /**
     * @param array<string, mixed> $params
     * @param array<string, mixed> $context
     * @return array{handled: bool, success: bool, fallback: bool, message: string|null, result_rows: array<int, mixed>}
     */
    public function execute(string $actionKey, array $params, array $context = []): array
    {
        if (! (bool) SettingHelper::get('custom_procedures_enabled', false)) {
            return [
                'handled' => false,
                'success' => false,
                'fallback' => true,
                'message' => 'custom_procedures_disabled',
                'result_rows' => [],
            ];
        }

        $mapping = ProcedureMapping::query()
            ->where('action', $actionKey)
            ->where('is_active', true)
            ->first();

        if ($mapping === null || blank($mapping->procedure_name)) {
            return [
                'handled' => false,
                'success' => false,
                'fallback' => true,
                'message' => 'procedure_mapping_inactive',
                'result_rows' => [],
            ];
        }

        return $this->executeMappedProcedure(
            mapping: $mapping,
            params: $params,
            context: $context,
            fallbackOnFailure: (bool) $mapping->use_fallback,
            collectResultRows: false,
        );
    }

    /**
     * @param array<string, mixed> $params
     * @return array{handled: bool, success: bool, fallback: bool, message: string|null, result_rows: array<int, mixed>}
     */
    public function test(string $actionKey, array $params): array
    {
        $mapping = $this->getOrCreateMapping($actionKey);

        if (blank($mapping->procedure_name)) {
            return [
                'handled' => false,
                'success' => false,
                'fallback' => false,
                'message' => 'procedure_name_missing',
                'result_rows' => [],
            ];
        }

        return $this->executeMappedProcedure(
            mapping: $mapping,
            params: $params,
            context: ['is_test' => true],
            fallbackOnFailure: false,
            collectResultRows: true,
        );
    }

    /**
     * @param array<int, array{laravel_key: string, procedure_param: string, position: int}> $parameterMap
     * @param array<string, mixed> $params
     * @return array{handled: bool, success: bool, fallback: bool, message: string|null, result_rows: array<int, mixed>}
     */
    public function testWithConfig(
        string $actionKey,
        string $procedureName,
        string $databaseConnection,
        array $parameterMap,
        array $params,
    ): array {
        $mapping = $this->getOrCreateMapping($actionKey);

        if (blank($procedureName)) {
            return [
                'handled' => false,
                'success' => false,
                'fallback' => false,
                'message' => 'procedure_name_missing',
                'result_rows' => [],
            ];
        }

        $mapping->procedure_name = $procedureName;
        $mapping->database_connection = $databaseConnection !== '' ? $databaseConnection : DatabaseNameEnums::SRO_SHARD->value;
        $mapping->parameter_map = $parameterMap;

        return $this->executeMappedProcedure(
            mapping: $mapping,
            params: $params,
            context: ['is_test' => true, 'unsaved_config' => true],
            fallbackOnFailure: false,
            collectResultRows: true,
        );
    }

    /**
     * @return array<string, string>
     */
    public function listProcedureNames(string $connection): array
    {
        if ($connection === '') {
            return [];
        }

        try {
            $driver = DB::connection($connection)->getDriverName();

            if ($driver === 'sqlsrv') {
                $rows = DB::connection($connection)->select(
                    'SELECT s.name AS schema_name, p.name AS procedure_name
                     FROM sys.procedures p
                     INNER JOIN sys.schemas s ON s.schema_id = p.schema_id
                     ORDER BY s.name, p.name'
                );

                $options = [];
                foreach ($rows as $row) {
                    $schema = (string) ($row->schema_name ?? 'dbo');
                    $name = (string) ($row->procedure_name ?? '');
                    if ($name === '') {
                        continue;
                    }

                    $qualified = sprintf('[%s].[%s]', $schema, $name);
                    $options[$qualified] = $qualified;
                }

                return $options;
            }

            if ($driver === 'mysql') {
                $rows = DB::connection($connection)->select(
                    'SELECT ROUTINE_NAME AS procedure_name
                     FROM information_schema.routines
                     WHERE ROUTINE_TYPE = ? AND ROUTINE_SCHEMA = DATABASE()
                     ORDER BY ROUTINE_NAME',
                    ['PROCEDURE']
                );

                $options = [];
                foreach ($rows as $row) {
                    $name = (string) ($row->procedure_name ?? '');
                    if ($name !== '') {
                        $options[$name] = $name;
                    }
                }

                return $options;
            }
        } catch (Throwable) {
            return [];
        }

        return [];
    }

    /**
     * @param array<string, mixed> $params
     * @param array<string, mixed> $context
     * @return array{handled: bool, success: bool, fallback: bool, message: string|null, result_rows: array<int, mixed>}
     */
    private function executeMappedProcedure(
        ProcedureMapping $mapping,
        array $params,
        array $context,
        bool $fallbackOnFailure,
        bool $collectResultRows,
    ): array {
        try {
            $connection = $mapping->database_connection ?: DatabaseNameEnums::SRO_SHARD->value;
            [$sql, $bindings, $mappedPayload] = $this->buildExecSql($mapping, $params, $connection);

            $resultRows = [];
            if ($collectResultRows) {
                try {
                    $resultRows = DB::connection($connection)->select($sql, $bindings);
                } catch (Throwable) {
                    DB::connection($connection)->statement($sql, $bindings);
                    $resultRows = [];
                }
            } else {
                DB::connection($connection)->statement($sql, $bindings);
            }

            $this->logCall(
                action: $mapping->action,
                procedureName: $mapping->procedure_name,
                databaseConnection: $connection,
                inputPayload: $params,
                mappedPayload: $mappedPayload,
                context: $context,
                success: true,
                fallbackUsed: false,
                errorMessage: null,
            );

            return [
                'handled' => true,
                'success' => true,
                'fallback' => false,
                'message' => null,
                'result_rows' => $resultRows,
            ];
        } catch (Throwable $exception) {
            report($exception);

            $this->logCall(
                action: $mapping->action,
                procedureName: $mapping->procedure_name,
                databaseConnection: $mapping->database_connection,
                inputPayload: $params,
                mappedPayload: null,
                context: $context,
                success: false,
                fallbackUsed: $fallbackOnFailure,
                errorMessage: $exception->getMessage(),
            );

            return [
                'handled' => true,
                'success' => false,
                'fallback' => $fallbackOnFailure,
                'message' => $exception->getMessage(),
                'result_rows' => [],
            ];
        }
    }

    /**
     * @param array<string, mixed> $params
     * @return array{0: string, 1: array<int, mixed>, 2: array<string, mixed>}
     */
    private function buildExecSql(ProcedureMapping $mapping, array $params, string $connection): array
    {
        $procedureName = $this->resolveProcedureName($connection, trim((string) $mapping->procedure_name));

        $segments = [];
        $bindings = [];
        $mappedPayload = [];

        foreach ($mapping->orderedParameterMap() as $row) {
            $source = trim((string) ($row['laravel_key'] ?? ''));
            $param = trim((string) ($row['procedure_param'] ?? ''));

            if ($source === '') {
                continue;
            }

            $value = array_key_exists($source, $params)
                ? $params[$source]
                : ($row['default_value'] ?? null);
            $bindings[] = $value;

            if ($param !== '') {
                $segments[] = "{$param} = ?";
                $mappedPayload[$param] = $value;
            } else {
                $segments[] = '?';
                $mappedPayload[$source] = $value;
            }
        }

        if ($segments === []) {
            throw new InvalidArgumentException('No parameter mapping configured for this action.');
        }

        $sql = sprintf('EXEC %s %s', $procedureName, implode(', ', $segments));

        return [$sql, $bindings, $mappedPayload];
    }

    private function resolveProcedureName(string $connection, string $input): string
    {
        if (! preg_match('/^[A-Za-z0-9_\.\[\]]+$/', $input)) {
            throw new InvalidArgumentException('Invalid procedure name format.');
        }

        $driver = DB::connection($connection)->getDriverName();

        if ($driver !== 'sqlsrv') {
            return $input;
        }

        $normalizedInput = str_replace(['[', ']'], '', $input);

        $rows = DB::connection($connection)->select(
            'SELECT TOP 1 s.name AS schema_name, p.name AS procedure_name
             FROM sys.procedures p
             INNER JOIN sys.schemas s ON s.schema_id = p.schema_id
             WHERE p.name = ? OR (s.name + \'\.\' + p.name) = ?
             ORDER BY CASE WHEN (s.name + \'\.\' + p.name) = ? THEN 0 ELSE 1 END, s.name, p.name',
            [$normalizedInput, $normalizedInput, $normalizedInput]
        );

        if ($rows === []) {
            throw new InvalidArgumentException(sprintf('Procedure "%s" was not found on connection "%s".', $input, $connection));
        }

        $row = $rows[0];
        $schema = (string) ($row->schema_name ?? 'dbo');
        $name = (string) ($row->procedure_name ?? '');

        if ($name === '') {
            throw new InvalidArgumentException(sprintf('Procedure "%s" was not found on connection "%s".', $input, $connection));
        }

        return sprintf('[%s].[%s]', $schema, $name);
    }

    /**
     * @param array<string, mixed>|null $mappedPayload
     * @param array<string, mixed> $context
     * @param array<string, mixed> $inputPayload
     */
    private function logCall(
        string $action,
        ?string $procedureName,
        ?string $databaseConnection,
        array $inputPayload,
        ?array $mappedPayload,
        array $context,
        bool $success,
        bool $fallbackUsed,
        ?string $errorMessage,
    ): void {
        ProcedureLog::query()->create([
            'action' => $action,
            'procedure_name' => $procedureName,
            'database_connection' => $databaseConnection,
            'input_payload' => $inputPayload,
            'mapped_payload' => $mappedPayload,
            'context' => $context,
            'success' => $success,
            'fallback_used' => $fallbackUsed,
            'error_message' => $errorMessage,
        ]);
    }

    /**
     * @return array<int, ProcedurableAction>
     */
    private function registeredActions(): array
    {
        return [
            app(WebmallPurchaseAction::class),
        ];
    }
}
