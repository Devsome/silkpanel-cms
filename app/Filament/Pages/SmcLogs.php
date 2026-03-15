<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use SilkPanel\SilkroadModels\Models\Account\SmcLog;

class SmcLogs extends Page implements HasTable
{
    use InteractsWithTable;

    protected const KNOWN_CATEGORIES = [
        1 => 'Punishment / Moderation',
        9 => 'Authentication',
        11 => 'Content Management',
        12 => 'Security / Permissions',
        13 => 'Server Configuration',
        14 => 'Live GM Actions',
        15 => 'Player Monitoring / Queries',
        21 => 'Account / Character Lookup',
        22 => 'Character / Item Management',
        23 => 'Game Events',
        24 => 'Job System Punishment',
        255 => 'Chat / Communication',
    ];

    protected const UNKNOWN_CATEGORY_FILTER = '__unknown';

    protected static ?string $model = SmcLog::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQueueList;

    protected static string | \UnitEnum | null $navigationGroup = 'Silkroad';

    protected static ?int $navigationSort = 100;

    protected string $view = 'filament.pages.smc-logs';

    public static function getNavigationLabel(): string
    {
        return __('filament/smcs.navigation');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(SmcLog::query())
            ->defaultSort('dLogDate', 'desc')
            ->columns([
                TextColumn::make('szUserID')
                    ->label(__('filament/smcs.table.szUserID')),
                TextColumn::make('Catagory')
                    ->label(__('filament/smcs.table.Catagory')),
                TextColumn::make('szLog')
                    ->label(__('filament/smcs.table.szLog'))
                    ->searchable(isIndividual: true),
                TextColumn::make('dLogDate')
                    ->label(__('filament/smcs.table.dLogDate'))
                    ->dateTime(),
            ])
            ->filters([
                SelectFilter::make(__('filament/smcs.filter.category'))
                    ->label(__('filament/smcs.filter.category'))
                    ->options(self::KNOWN_CATEGORIES + [self::UNKNOWN_CATEGORY_FILTER => 'Category Unbekannt'])
                    ->query(function (Builder $query, array $data): Builder {
                        $value = $data['value'] ?? null;

                        if ($value === null || $value === '') {
                            return $query;
                        }

                        if ($value === self::UNKNOWN_CATEGORY_FILTER) {
                            return $query->whereNotIn('Catagory', array_keys(self::KNOWN_CATEGORIES));
                        }

                        return $query->where('Catagory', $value);
                    }),
            ]);
    }
}
