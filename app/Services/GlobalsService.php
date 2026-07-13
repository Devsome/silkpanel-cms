<?php

namespace App\Services;

use App\Enums\DatabaseNameEnums;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class GlobalsService
{
    private const CACHE_TTL = 60;

    /**
     * The latest global (yell) chat messages.
     *
     * Currently only iSRO is supported, because it ships the standard
     * `_LogChatMessage` log table. On vSRO there is no standard way to read
     * global messages — support for a custom (Page Settings configurable)
     * table/procedure returning CharName, Message and Timestamp can be wired
     * into {@see self::customSource()} later without touching callers.
     *
     * @return Collection<int, object>
     */
    public function latest(int $limit = 10): Collection
    {
        $limit = max(1, min($limit, 50));

        if (config('silkpanel.version') === 'isro') {
            return $this->isroGlobals($limit);
        }

        return $this->customSource($limit);
    }

    /**
     * The latest global (yell) messages sent by a single character.
     *
     * iSRO only (see {@see self::latest()} for the rationale and the future
     * custom-source extension point).
     *
     * @return Collection<int, object>
     */
    public function forCharacter(string $charName, int $limit = 10): Collection
    {
        $charName = trim($charName);
        $limit = max(1, min($limit, 50));

        if ($charName === '' || config('silkpanel.version') !== 'isro') {
            return collect();
        }

        try {
            return Cache::remember('globals_char_' . md5($charName) . "_{$limit}", self::CACHE_TTL, function () use ($charName, $limit) {
                return DB::connection(DatabaseNameEnums::SRO_LOG->value)
                    ->table('dbo._LogChatMessage')
                    ->select(['CharName', 'EventTime', 'Comment'])
                    ->where('TargetName', '[YELL]')
                    ->where('CharName', $charName)
                    ->orderByDesc('EventTime')
                    ->limit($limit)
                    ->get();
            });
        } catch (\Throwable) {
            return collect();
        }
    }

    /**
     * iSRO: read the latest yell messages from `_LogChatMessage`.
     *
     * @return Collection<int, object>
     */
    private function isroGlobals(int $limit): Collection
    {
        try {
            return Cache::remember("globals_widget_{$limit}", self::CACHE_TTL, function () use ($limit) {
                $shardDb = DB::connection(DatabaseNameEnums::SRO_SHARD->value)->getDatabaseName();

                return DB::connection(DatabaseNameEnums::SRO_LOG->value)
                    ->table('dbo._LogChatMessage as log')
                    ->select([
                        'c.CharID',
                        'c.RefObjID',
                        'log.CharName',
                        'log.EventTime',
                        'log.Comment',
                    ])
                    ->leftJoin(DB::raw("{$shardDb}.dbo._Char as c"), function ($join) {
                        $join->on(
                            DB::raw('c.CharName16 COLLATE Latin1_General_CI_AS'),
                            '=',
                            DB::raw('log.CharName COLLATE Latin1_General_CI_AS'),
                        );
                    })
                    ->where('log.TargetName', '[YELL]')
                    ->orderByDesc('log.EventTime')
                    ->limit($limit)
                    ->get();
            });
        } catch (\Throwable) {
            return collect();
        }
    }

    /**
     * vSRO / custom servers: no standard global log exists.
     *
     * Placeholder for a future Page Settings configurable source (custom table
     * or stored procedure returning CharName, Message and Timestamp).
     *
     * @return Collection<int, object>
     */
    private function customSource(int $limit): Collection
    {
        return collect();
    }
}
