<?php

namespace App\Contracts;

interface ProcedurableAction
{
    public function key(): string;

    public function label(): string;

    /**
     * @return array<int, array{laravel_key: string, procedure_param: string, position: int}>
     */
    public function defaultParameterMap(): array;
}
