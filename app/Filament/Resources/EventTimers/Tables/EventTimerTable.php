<?php

namespace App\Filament\Resources\EventTimers\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EventTimerTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('filament/event-timers.table.name'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('type')
                    ->label(__('filament/event-timers.table.type'))
                    ->badge()
                    ->formatStateUsing(fn(?string $state) => match ($state) {
                        'hourly' => __('filament/event-timers.type.hourly'),
                        'weekly' => __('filament/event-timers.type.weekly'),
                        'static' => __('filament/event-timers.type.static'),
                        default => $state,
                    })
                    ->color(fn(?string $state) => match ($state) {
                        'hourly' => 'success',
                        'weekly' => 'info',
                        'static' => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('icon')
                    ->label(__('filament/event-timers.table.icon')),

                TextColumn::make('id')
                    ->label(__('filament/event-timers.table.schedule'))
                    ->formatStateUsing(function ($record) {
                        if ($record->type === 'static') {
                            return $record->time;
                        }

                        if (is_array($record->hours) && count($record->hours) > 0) {
                            $hourList = collect($record->hours)->sort()->map(fn($h) => str_pad($h, 2, '0', STR_PAD_LEFT) . ':' . str_pad($record->min, 2, '0', STR_PAD_LEFT))->implode(', ');
                            return $hourList;
                        }

                        if (is_array($record->days) && count($record->days) > 0 && $record->hour !== null) {
                            $dayList = implode(', ', $record->days);
                            return $dayList . ' @ ' . str_pad($record->hour, 2, '0', STR_PAD_LEFT) . ':' . str_pad($record->min, 2, '0', STR_PAD_LEFT);
                        }

                        return '-';
                    })
                    ->wrap(),

                TextColumn::make('updated_at')
                    ->label(__('filament/event-timers.table.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateIcon('heroicon-o-clock')
            ->emptyStateHeading(__('filament/event-timers.table.empty'))
            ->emptyStateDescription(__('filament/event-timers.table.empty_description'));
    }
}
