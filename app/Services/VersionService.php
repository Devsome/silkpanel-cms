<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class VersionService
{
    private const REMOTE_CACHE_KEY = 'silkpanel_remote_version';
    private const REMOTE_CACHE_TTL = 3600;
    private const REMOTE_URL = 'https://raw.githubusercontent.com/Devsome/silkpanel-cms/master/storage/app/version.json';

    public function getLocal(): array
    {
        $path = storage_path('app/version.json');

        if (! file_exists($path)) {
            return ['current' => 'unknown', 'changelog' => []];
        }

        return json_decode(file_get_contents($path), true) ?? ['current' => 'unknown', 'changelog' => []];
    }

    public function getRemote(): ?array
    {
        return Cache::remember(self::REMOTE_CACHE_KEY, self::REMOTE_CACHE_TTL, function () {
            try {
                $response = Http::timeout(5)->get(self::REMOTE_URL);
                return $response->ok() ? $response->json() : null;
            } catch (\Exception) {
                return null;
            }
        });
    }

    public function forgetRemoteCache(): void
    {
        Cache::forget(self::REMOTE_CACHE_KEY);
    }

    public function getLocalVersion(): string
    {
        return $this->getLocal()['current'] ?? 'unknown';
    }

    public function getRemoteVersion(): string
    {
        return $this->getRemote()['current'] ?? 'unknown';
    }

    public function isUpToDate(): bool
    {
        $remote = $this->getRemoteVersion();

        if ($remote === 'unknown') {
            return true;
        }

        return version_compare($this->getLocalVersion(), $remote, '>=');
    }

    public function getVersionsBehind(): int
    {
        $remote = $this->getRemote();

        if (! $remote) {
            return 0;
        }

        $localVersion = $this->getLocalVersion();
        $remoteChangelog = $remote['changelog'] ?? [];

        return count(array_filter(
            array_keys($remoteChangelog),
            fn($v) => version_compare($v, $localVersion, '>')
        ));
    }

    /**
     * Returns changelog entries from the remote that are newer than the local version,
     * sorted newest-first.
     */
    public function getMissedVersions(): array
    {
        $remote = $this->getRemote();

        if (! $remote) {
            return [];
        }

        $localVersion = $this->getLocalVersion();
        $changelog = $remote['changelog'] ?? [];

        $missed = array_filter(
            $changelog,
            fn($_, $v) => version_compare($v, $localVersion, '>'),
            ARRAY_FILTER_USE_BOTH
        );

        uksort($missed, fn($a, $b) => version_compare($b, $a));

        return $missed;
    }

    /**
     * Returns the local changelog sorted newest-first.
     */
    public function getLocalChangelog(): array
    {
        $changelog = $this->getLocal()['changelog'] ?? [];
        uksort($changelog, fn($a, $b) => version_compare($b, $a));

        return $changelog;
    }

    public function hasUserSeenCurrentVersion(): bool
    {
        $userId = auth()->id();

        if (! $userId) {
            return true;
        }

        $seen = cache()->get("sp_version_seen_{$userId}");

        return $seen === $this->getLocalVersion();
    }

    public function markCurrentVersionAsSeen(): void
    {
        $userId = auth()->id();

        if (! $userId) {
            return;
        }

        cache()->put("sp_version_seen_{$userId}", $this->getLocalVersion(), now()->addYear());
    }
}
