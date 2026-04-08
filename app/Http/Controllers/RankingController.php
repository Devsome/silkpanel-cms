<?php

namespace App\Http\Controllers;

use SilkPanel\SilkroadModels\Models\Shard\AbstractChar;
use SilkPanel\SilkroadModels\Models\Shard\Guild;
use SilkPanel\SilkroadModels\Service\InventoryService;

class RankingController extends Controller
{
    private const EQUIPMENT_MAX_SLOTS = 12;
    private const EQUIPMENT_MIN_SLOTS = 0;
    private const EQUIPMENT_NOT_SLOTS = [8];

    public function showCharacter(int $id)
    {
        /** @var AbstractChar $charModel */
        $charModel = app(AbstractChar::class);

        $character = $charModel::query()
            ->where('CharID', $id)
            ->where('deleted', 0)
            ->firstOrFail();

        $character->load('guild');

        $inventoryService = app(InventoryService::class);
        $equipment = $inventoryService->getInventorySet(
            $character->CharID,
            self::EQUIPMENT_MAX_SLOTS,
            self::EQUIPMENT_MIN_SLOTS,
            self::EQUIPMENT_NOT_SLOTS,
        );

        $avatar = $inventoryService->getAvatarSet($character->CharID);

        return view('template::ranking.character-detail', [
            'character' => $character,
            'equipment' => $equipment,
            'avatar' => $avatar,
            'characterImage2d' => $character->avatarUrl,
            'characterFullImage2d' => $character->characterUrl,
        ]);
    }

    public function showGuild(int $id)
    {
        $guild = Guild::query()
            ->where('ID', $id)
            ->firstOrFail();

        $members = $guild->guildMembers()
            ->orderBy('JoinDate', 'asc')
            ->get();

        return view('template::ranking.guild-detail', [
            'guild' => $guild,
            'members' => $members,
        ]);
    }
}
