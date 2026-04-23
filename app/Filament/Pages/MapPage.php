<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class MapPage extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedGlobeAlt;

    protected static string|\UnitEnum|null $navigationGroup = 'Silkroad';

    protected static ?int $navigationSort = 10;

    protected string $view = 'filament.pages.map-page';

    public static function getNavigationLabel(): string
    {
        return __('filament/map.navigation');
    }

    /**
     * Map configuration read from the settings table.
     * Defaults are safe values that work without any custom tile server.
     */
    public function getMapConfig(): array
    {
        return [
            'refresh_interval' => (int) Setting::get('map_refresh_interval', 30),
            'default_lat'      => (float) Setting::get('map_default_lat', 24.576),
            'default_lng'      => (float) Setting::get('map_default_lng', 24.576),
            'default_zoom'     => (int) Setting::get('map_default_zoom', 2),
            'tile_url'         => (string) Setting::get('map_tile_url', ''),
            'show_gm_chars'    => (bool) Setting::get('map_show_gm_chars', true),
            'api_url'          => route('api.admin.map.characters'),
            'debug_overlay'    => true, // admin always sees the debug overlay
        ];
    }
}
