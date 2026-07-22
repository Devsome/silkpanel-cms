<?php

namespace App\Filament\Clusters\Settings\Pages;

use App\Helpers\LicenseHelper;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;
use SilkPanel\SilkroadModels\Models\Shard\AbstractChar;

class Map extends AbstractSettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedGlobeAlt;

    protected static ?int $navigationSort = 90;

    public static function getNavigationLabel(): string
    {
        return __('filament/map.settings.tab');
    }

    public function isLocked(): bool
    {
        return ! LicenseHelper::isValid();
    }

    public function getLockedDescription(): string
    {
        return __('filament/settings.locked.license_required_map');
    }

    protected function getFormColumns(): int|array
    {
        return 1;
    }

    protected function getSettingKeys(): array
    {
        return [
            'map_frontend_enabled',
            'map_max_characters',
            'map_excluded_chars',
        ];
    }

    protected function getFormSchema(): array
    {
        return [
            Section::make(__('filament/map.settings.section'))
                ->schema([
                    Toggle::make('map_frontend_enabled')
                        ->label(__('filament/map.settings.frontend_enabled'))
                        ->helperText(__('filament/map.settings.frontend_enabled_description'))
                        ->default(false),

                    TextInput::make('map_max_characters')
                        ->label(__('filament/map.settings.max_characters'))
                        ->helperText(__('filament/map.settings.max_characters_description'))
                        ->numeric()
                        ->minValue(1)
                        ->maxValue(5000)
                        ->default(500),

                    Select::make('map_excluded_chars')
                        ->label(__('filament/map.settings.excluded_chars'))
                        ->helperText(__('filament/map.settings.excluded_chars_description'))
                        ->multiple()
                        ->searchable()
                        ->getSearchResultsUsing(function (string $search): array {
                            /** @var AbstractChar $charModel */
                            $charModel = app(AbstractChar::class);

                            return $charModel::query()
                                ->where('CharName16', 'like', "%{$search}%")
                                ->where('Deleted', 0)
                                ->limit(20)
                                ->pluck('CharName16', 'CharName16')
                                ->toArray();
                        })
                        ->getOptionLabelsUsing(function (array $values): array {
                            /** @var AbstractChar $charModel */
                            $charModel = app(AbstractChar::class);

                            return $charModel::query()
                                ->whereIn('CharName16', $values)
                                ->where('Deleted', 0)
                                ->pluck('CharName16', 'CharName16')
                                ->toArray();
                        })
                        ->columnSpanFull(),
                ])->secondary()->columns(1),
        ];
    }
}
