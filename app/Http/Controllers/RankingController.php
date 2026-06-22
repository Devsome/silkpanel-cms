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

    public function showCharacter(string $idOrSlug)
    {
        /** @var AbstractChar $charModel */
        $charModel = app(AbstractChar::class);

        $character = null;

        if (ctype_digit($idOrSlug)) {
            $character = $charModel::query()
                ->where('CharID', $idOrSlug)
                ->where('deleted', 0)
                ->first();
        }

        if (!$character) {
            $charId = $charModel::query()
                ->where('deleted', 0)
                ->get(['CharID', 'CharName16'])
                ->first(fn (AbstractChar $char) => $char->slug === $idOrSlug)
                ?->CharID;

            $character = $charId
                ? $charModel::query()->where('CharID', $charId)->where('deleted', 0)->first()
                : null;
        }

        abort_unless($character, 404);

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

    public function showGuild(string $idOrSlug)
    {
        $guild = null;

        if (ctype_digit($idOrSlug)) {
            $guild = Guild::query()->where('ID', $idOrSlug)->first();
        }

        if (!$guild) {
            $guildId = Guild::query()
                ->get(['ID', 'Name'])
                ->first(fn (Guild $g) => $g->slug === $idOrSlug)
                ?->ID;

            $guild = $guildId ? Guild::query()->where('ID', $guildId)->first() : null;
        }

        abort_unless($guild, 404);

        $members = $guild->guildMembers()
            ->orderBy('JoinDate', 'asc')
            ->get();

        return view('template::ranking.guild-detail', [
            'guild' => $guild,
            'members' => $members,
        ]);
    }
}
