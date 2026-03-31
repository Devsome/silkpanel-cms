<?php

namespace App\Filament\Resources\Pages\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Support\Enums\TextSize;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;

class PagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label(__('filament/pages.table.id'))
                    ->sortable(),
                TextColumn::make('slug')
                    ->label(__('filament/pages.table.slug'))
                    ->searchable(),
                TextColumn::make('languages_status')
                    ->label(__('filament/pages.table.languages_status'))
                    ->badge()
                    ->size(TextSize::ExtraSmall)
                    ->color(fn(string $state): string => str_starts_with($state, __('filament/pages.translated'))
                        ? 'success'
                        : (str_starts_with($state, __('filament/pages.not_translated')) ? 'danger' : 'gray'))
                    ->separator(',')
                    ->listWithLineBreaks(),
                TextColumn::make('published_at')
                    ->label(__('filament/pages.table.published_at'))
                    ->dateTime()
                    ->description(fn($record) => Carbon::parse($record->published_at)->diffForHumans())
                    ->sortable(),
                TextColumn::make('deleted_at')
                    ->label(__('filament/pages.table.deleted_at'))
                    ->dateTime()
                    ->description(fn($record) => Carbon::parse($record->deleted_at)->diffForHumans())
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make()
                        ->label(__('filament/pages.table.delete'))
                        ->requiresConfirmation()
                        ->visible(fn($record) => $record->deleted_at === null),
                    RestoreAction::make()
                        ->label(__('filament/pages.table.restore'))
                        ->visible(fn($record) => $record->deleted_at !== null),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->emptyStateIcon('heroicon-o-document-text')
            ->emptyStateHeading(__('filament/pages.table.empty_heading'))
            ->emptyStateDescription(__('filament/pages.table.empty_description'));
    }
}
