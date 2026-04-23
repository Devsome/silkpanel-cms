<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use SilkPanel\SilkroadModels\Models\Log\LogEventChar;
use SilkPanel\SilkroadModels\Models\Shard\AbstractChar;
use SilkPanel\SilkroadModels\Models\Shard\Inventory;

class SilkroadMapService
{
    /**
     * Maximum number of characters returned by the API.
     */
    public const MAX_CHARACTERS = 500;

    /**
     * Retrieve all online character positions.
     */
    public function getOnlineCharacterPositions(bool $adminView = false): Collection
    {
        // All raw data is cached once. Views differ only in post-processing.
        $all = Cache::remember('map.online_characters.raw', 10, function (): Collection {
            // Step 1: Retrieve CharIDs of currently online characters from the log DB.
            // A character is online when its latest login/logout event is EventID = 4.
            $onlineCharIds = LogEventChar::selectRaw('CharID')
                ->whereIn('EventID', [4, 6])
                ->whereRaw(
                    'EventTime = (SELECT MAX(sub.EventTime) FROM dbo._LogEventChar AS sub WHERE sub.CharID = dbo._LogEventChar.CharID AND sub.EventID IN (4, 6))'
                )
                ->where('EventID', 4)
                ->pluck('CharID');

            if ($onlineCharIds->isEmpty()) {
                return collect();
            }

            // Step 2: Retrieve the character data from the shard DB.
            /** @var AbstractChar $charModel */
            $charModel = resolve(AbstractChar::class);

            $characters = $charModel::query()
                ->select(['CharID', 'CharName16', 'CurLevel', 'GuildID', 'LatestRegion', 'PosX', 'PosY', 'PosZ', 'LastLogout'])
                ->whereIn('CharID', $onlineCharIds)
                ->where('Deleted', 0)
                ->limit((int) Setting::get('map_max_characters', self::MAX_CHARACTERS))
                ->get();

            // Batch-check who has a job suit (slot 8 occupied) — single query, no N+1.
            $jobSuitSet = Inventory::whereIn('CharID', $characters->pluck('CharID'))
                ->where('Slot', 8)
                ->where('ItemID', '>', 0)
                ->pluck('CharID')
                ->flip()
                ->all();

            return $characters->map(function ($char) use ($jobSuitSet): array {
                return [
                    'char_id'      => (int) $char->CharID,
                    'name'         => $char->CharName16,
                    'level'        => (int) $char->CurLevel,
                    'pos_x'        => (float) $char->PosX,
                    'pos_y'        => (float) $char->PosZ,
                    'pos_z'        => (float) $char->PosY,
                    'region'       => (int) $char->LatestRegion,
                    'guild_id'     => $char->GuildID ? (int) $char->GuildID : null,
                    'has_job_suit' => isset($jobSuitSet[$char->CharID]),
                    'is_online'    => true,
                    'updated_at'   => $char->LastLogout ? (string) $char->LastLogout : null,
                ];
            });
        });

        if ($adminView) {
            // Admin sees everyone, including job-suit characters, with the has_job_suit flag.
            return $all;
        }

        // Frontend: remove job-suit characters entirely and strip the flag from the response
        // so that position data of hidden characters cannot be read from the network tab.
        // Also exclude any characters explicitly blocked via the admin setting.
        $excludedNames = collect((array) Setting::get('map_excluded_chars', []));

        return $all
            ->filter(function (array $c) use ($excludedNames): bool {
                if ($c['has_job_suit']) {
                    return false;
                }
                if ($excludedNames->isNotEmpty() && $excludedNames->contains($c['name'])) {
                    return false;
                }
                return true;
            })
            ->map(function (array $c): array {
                unset($c['has_job_suit']);
                return $c;
            })
            ->values();
    }

    /**
     * Flush the cached online character list.
     */
    public function flushCache(): void
    {
        Cache::forget('map.online_characters.raw');
        Cache::forget('map.all_characters.raw');
    }

    /**
     * Retrieve ALL character positions (online + offline) for the admin offline view.
     * Online characters are marked with is_online = true.
     */
    public function getAllCharacterPositions(): Collection
    {
        return Cache::remember('map.all_characters.raw', 10, function (): Collection {
            // Determine which characters are currently online (for the is_online flag).
            $onlineSet = LogEventChar::selectRaw('CharID')
                ->whereIn('EventID', [4, 6])
                ->whereRaw(
                    'EventTime = (SELECT MAX(sub.EventTime) FROM dbo._LogEventChar AS sub WHERE sub.CharID = dbo._LogEventChar.CharID AND sub.EventID IN (4, 6))'
                )
                ->where('EventID', 4)
                ->pluck('CharID')
                ->flip()
                ->all();

            /** @var AbstractChar $charModel */
            $charModel = resolve(AbstractChar::class);

            $characters = $charModel::query()
                ->select(['CharID', 'CharName16', 'CurLevel', 'GuildID', 'LatestRegion', 'PosX', 'PosY', 'PosZ', 'LastLogout'])
                ->where('Deleted', 0)
                ->limit((int) Setting::get('map_max_characters', self::MAX_CHARACTERS))
                ->get();

            if ($characters->isEmpty()) {
                return collect();
            }

            $jobSuitSet = Inventory::whereIn('CharID', $characters->pluck('CharID'))
                ->where('Slot', 8)
                ->where('ItemID', '>', 0)
                ->pluck('CharID')
                ->flip()
                ->all();

            return $characters->map(function ($char) use ($jobSuitSet, $onlineSet): array {
                return [
                    'char_id'      => (int) $char->CharID,
                    'name'         => $char->CharName16,
                    'level'        => (int) $char->CurLevel,
                    'pos_x'        => (float) $char->PosX,
                    'pos_y'        => (float) $char->PosZ,
                    'pos_z'        => (float) $char->PosY,
                    'region'       => (int) $char->LatestRegion,
                    'guild_id'     => $char->GuildID ? (int) $char->GuildID : null,
                    'has_job_suit' => isset($jobSuitSet[$char->CharID]),
                    'is_online'    => isset($onlineSet[$char->CharID]),
                    'updated_at'   => $char->LastLogout ? (string) $char->LastLogout : null,
                ];
            });
        });
    }
}
