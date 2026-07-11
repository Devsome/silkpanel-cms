<?php

namespace App\Actions\WebMarket;

use App\Contracts\ProcedurableAction;

class TransferFromWebStorageAction implements ProcedurableAction
{
    public const ACTION_KEY = 'web_market.transfer_from_storage';

    public function key(): string
    {
        return self::ACTION_KEY;
    }

    public function label(): string
    {
        return 'Web Market: Transfer Item from Web Storage';
    }

    /**
     * @return array<int, array{laravel_key: string, procedure_param: string, position: int}>
     */
    public function defaultParameterMap(): array
    {
        return [
            ['laravel_key' => 'player_id',    'procedure_param' => '@PlayerID',    'position' => 1],
            ['laravel_key' => 'character_id',  'procedure_param' => '@CharacterID', 'position' => 2],
            ['laravel_key' => 'item_id64',     'procedure_param' => '@ItemID64',    'position' => 3],
            ['laravel_key' => 'target_type',   'procedure_param' => '@TargetType',  'position' => 4],
        ];
    }
}
