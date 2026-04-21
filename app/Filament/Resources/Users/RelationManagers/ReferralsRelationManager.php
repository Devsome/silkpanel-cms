<?php

namespace App\Filament\Resources\Users\RelationManagers;

use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ReferralsRelationManager extends RelationManager
{
    protected static string $relationship = 'Referrals';

    protected static ?string $title = null;

    public static function getTitle(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): string
    {
        return __('filament/users.referrals.title');
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('referred.name')
                    ->label(__('filament/users.referrals.referred'))
                    ->state(
                        fn($record) => $record->referred?->shardUsers
                            ->filter(fn($c) => $c->CharID != 0 && $c->CharName16 !== 'dummy')
                            ->sortByDesc('CurLevel')
                            ->first()
                            ?->CharName16 ?? __('filament/users.referrals.no_character')
                    ),
                TextEntry::make('status')
                    ->label(__('filament/users.referrals.status'))
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => __('filament/users.referrals.status_' . $state))
                    ->color(fn(string $state): string => match ($state) {
                        'valid' => 'success',
                        default => 'warning',
                    }),
                TextEntry::make('silk_rewarded')
                    ->label(__('filament/users.referrals.silk_rewarded'))
                    ->numeric(),
                TextEntry::make('rewarded_at')
                    ->label(__('filament/users.referrals.rewarded_at'))
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('referrals')
            ->modifyQueryUsing(fn($query) => $query->with(['referred.shardUsers']))
            ->columns([
                TextColumn::make('referred.name')
                    ->label(__('filament/users.referrals.referred'))
                    ->state(
                        fn($record) => $record->referred?->shardUsers
                            ->filter(fn($c) => $c->CharID != 0 && $c->CharName16 !== 'dummy')
                            ->sortByDesc('CurLevel')
                            ->first()
                            ?->CharName16 ?? __('filament/users.referrals.no_character')
                    )
                    ->description(fn($record) => $record->referred?->name)
                    ->searchable(),
                TextColumn::make('status')
                    ->label(__('filament/users.referrals.status'))
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => __('filament/users.referrals.status_' . $state))
                    ->color(fn(string $state): string => match ($state) {
                        'valid' => 'success',
                        default => 'warning',
                    }),
                TextColumn::make('silk_rewarded')
                    ->label(__('filament/users.referrals.silk_rewarded'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('rewarded_at')
                    ->label(__('filament/users.referrals.rewarded_at'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                ]),
            ])
            ->toolbarActions([
                //
            ]);
    }
}
