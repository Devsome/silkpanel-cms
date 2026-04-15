<?php

namespace App\Filament\Resources\EventTimers;

use App\Filament\Resources\EventTimers\Pages\CreateEventTimer;
use App\Filament\Resources\EventTimers\Pages\EditEventTimer;
use App\Filament\Resources\EventTimers\Pages\ListEventTimers;
use App\Filament\Resources\EventTimers\Schemas\EventTimerForm;
use App\Filament\Resources\EventTimers\Tables\EventTimerTable;
use App\Models\EventTimer;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class EventTimerResource extends Resource
{
    protected static ?string $model = EventTimer::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClock;

    protected static string|\UnitEnum|null $navigationGroup = 'Configuration';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 50;

    public static function getNavigationLabel(): string
    {
        return __('filament/event-timers.navigation_label');
    }

    public static function getModelLabel(): string
    {
        return __('filament/event-timers.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament/event-timers.plural_model_label');
    }

    public static function form(Schema $schema): Schema
    {
        return EventTimerForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EventTimerTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEventTimers::route('/'),
            'create' => CreateEventTimer::route('/create'),
            'edit' => EditEventTimer::route('/{record}/edit'),
        ];
    }
}
