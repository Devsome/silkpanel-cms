<?php

namespace App\Filament\Clusters;

use BackedEnum;
use Filament\Clusters\Cluster;
use Filament\Support\Icons\Heroicon;

class Settings extends Cluster
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static string|\UnitEnum|null $navigationGroup = 'Configuration';

    protected static ?int $navigationSort = 50;

    public static function getNavigationLabel(): string
    {
        return __('filament/settings.navigation');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament/settings.navigation_group');
    }

    public static function getClusterBreadcrumb(): ?string
    {
        return __('filament/settings.navigation');
    }
}
