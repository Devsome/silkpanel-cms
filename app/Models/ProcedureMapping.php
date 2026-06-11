<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $action
 * @property string $action_label
 * @property string|null $procedure_name
 * @property string $database_connection
 * @property array<int, array{laravel_key: string, procedure_param: string, position: int}>|null $parameter_map
 * @property bool $is_active
 * @property bool $use_fallback
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class ProcedureMapping extends Model
{
    protected $table = 'procedure_mappings';

    protected $fillable = [
        'action',
        'action_label',
        'procedure_name',
        'database_connection',
        'parameter_map',
        'is_active',
        'use_fallback',
    ];

    protected $casts = [
        'parameter_map' => 'array',
        'is_active' => 'boolean',
        'use_fallback' => 'boolean',
    ];

    /**
     * @return array<int, array{laravel_key: string, procedure_param: string, position: int}>
     */
    public function orderedParameterMap(): array
    {
        $map = $this->parameter_map ?? [];

        usort($map, fn(array $a, array $b): int => (int) ($a['position'] ?? 0) <=> (int) ($b['position'] ?? 0));

        return $map;
    }
}
