<?php

namespace App\Filament\Pages;

use App\Services\VersionService;
use Filament\Pages\Page;

class WhatsNew extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-sparkles';
    protected static ?string $navigationLabel = "What's New";
    protected static ?string $title = "What's New";
    protected static ?string $slug = 'whats-new';
    protected static ?int $navigationSort = 99;

    protected string $view = 'filament.pages.whats-new';

    public array $localChangelog = [];
    public array $missedVersions = [];
    public string $localVersion = 'unknown';
    public string $remoteVersion = 'unknown';
    public bool $isUpToDate = true;
    public int $versionsBehind = 0;

    public function mount(): void
    {
        $service = app(VersionService::class);

        $this->localVersion = $service->getLocalVersion();
        $this->remoteVersion = $service->getRemoteVersion();
        $this->isUpToDate = $service->isUpToDate();
        $this->versionsBehind = $service->getVersionsBehind();
        $this->localChangelog = $service->getLocalChangelog();
        $this->missedVersions = $service->getMissedVersions();

        $service->markCurrentVersionAsSeen();
    }

    public static function getNavigationBadge(): ?string
    {
        $behind = app(VersionService::class)->getVersionsBehind();
        return $behind > 0 ? (string) $behind : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}
