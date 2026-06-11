<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $action
 * @property string|null $procedure_name
 * @property string|null $database_connection
 * @property array<string, mixed>|null $input_payload
 * @property array<string, mixed>|null $mapped_payload
 * @property array<string, mixed>|null $context
 * @property bool $success
 * @property bool $fallback_used
 * @property string|null $error_message
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class ProcedureLog extends Model
{
    protected $table = 'procedure_logs';

    protected $fillable = [
        'action',
        'procedure_name',
        'database_connection',
        'input_payload',
        'mapped_payload',
        'context',
        'success',
        'fallback_used',
        'error_message',
    ];

    protected $casts = [
        'input_payload' => 'array',
        'mapped_payload' => 'array',
        'context' => 'array',
        'success' => 'boolean',
        'fallback_used' => 'boolean',
    ];
}
