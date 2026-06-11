<?php

namespace App\Actions;

use App\Contracts\ProcedurableAction;

class WebmallPurchaseAction implements ProcedurableAction
{
    public const ACTION_KEY = 'webmall.buy_item';

    public function key(): string
    {
        return self::ACTION_KEY;
    }

    public function label(): string
    {
        return 'WebMall: Buy Item';
    }

    /**
     * @return array<int, array{laravel_key: string, procedure_param: string, position: int}>
     */
    public function defaultParameterMap(): array
    {
        return [
            ['laravel_key' => 'player_id', 'procedure_param' => '@PlayerID', 'position' => 1],
            ['laravel_key' => 'character_id', 'procedure_param' => '@CharacterID', 'position' => 2],
            ['laravel_key' => 'item_id', 'procedure_param' => '@ItemID', 'position' => 3],
            ['laravel_key' => 'price_type', 'procedure_param' => '@PriceType', 'position' => 4],
            ['laravel_key' => 'price_amount', 'procedure_param' => '@PriceAmount', 'position' => 5],
        ];
    }
}
