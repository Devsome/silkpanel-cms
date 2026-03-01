<?php

namespace App\Filament\Resources\Downloads\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DownloadsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('filament/downloads.table.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('link')
                    ->label(__('filament/downloads.table.link'))
                    ->formatStateUsing(fn($state) => "<a href='{$state}' target='_blank'>{$state}</a>")
                    ->html(),
                TextColumn::make('description')
                    ->label(__('filament/downloads.table.description'))
                    ->limit(30, end: ' ...')
                    ->toggleable(isToggledHiddenByDefault: false),
                ImageColumn::make('image')
                    ->label(__('filament/downloads.table.image'))
                    ->extraImgAttributes([
                        'loading' => 'lazy',
                    ])
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateIcon('heroicon-o-arrow-down-tray')
            ->emptyStateHeading(__('filament/downloads.table.empty'))
            ->emptyStateDescription(__('filament/downloads.table.empty_description'));
    }
}
