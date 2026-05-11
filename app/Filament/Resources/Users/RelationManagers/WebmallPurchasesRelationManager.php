<?php

namespace App\Filament\Resources\Users\RelationManagers;

use App\Enums\SilkTypeEnum;
use App\Enums\SilkTypeIsroEnum;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class WebmallPurchasesRelationManager extends RelationManager
{
    protected static string $relationship = 'webmallPurchases';

    protected static ?string $title = null;

    public static function getTitle(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): string
    {
        return __('filament/webmall.user_purchases_title');
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label(__('filament/webmall.col_id'))
                    ->sortable(),
                TextColumn::make('character_name')
                    ->label(__('filament/webmall.col_character'))
                    ->searchable(),
                TextColumn::make('item_name')
                    ->label(__('filament/webmall.col_item'))
                    ->searchable(),
                TextColumn::make('price_type')
                    ->label(__('filament/webmall.col_price_type'))
                    ->badge()
                    ->formatStateUsing(function ($state): string {
                        return match (true) {
                            $state === 'gold'                                        => 'Gold',
                            SilkTypeIsroEnum::tryFrom((int) $state) !== null         => SilkTypeIsroEnum::from((int) $state)->getLabel(),
                            SilkTypeEnum::tryFrom((string) $state) !== null          => SilkTypeEnum::from((string) $state)->getLabel(),
                            default                                                  => (string) $state,
                        };
                    })
                    ->color(function ($state): string {
                        return match (true) {
                            $state === 'gold'                                                             => 'danger',
                            SilkTypeIsroEnum::tryFrom((int) $state) === SilkTypeIsroEnum::SILK_TYPE_NORMAL  => 'success',
                            SilkTypeIsroEnum::tryFrom((int) $state) === SilkTypeIsroEnum::SILK_TYPE_PREMIUM => 'warning',
                            SilkTypeEnum::tryFrom((string) $state) === SilkTypeEnum::SILK_OWN               => 'success',
                            SilkTypeEnum::tryFrom((string) $state) === SilkTypeEnum::SILK_GIFT              => 'info',
                            SilkTypeEnum::tryFrom((string) $state) === SilkTypeEnum::SILK_POINT             => 'warning',
                            default                                                                         => 'gray',
                        };
                    }),
                TextColumn::make('price_value')
                    ->label(__('filament/webmall.col_price_paid'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('filament/webmall.col_date'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('price_type')
                    ->label(__('filament/webmall.filter_price_type'))
                    ->options(
                        collect(SilkTypeEnum::cases())->mapWithKeys(fn($c) => [$c->value => $c->getLabel()])->toArray()
                            + collect(SilkTypeIsroEnum::cases())->mapWithKeys(fn($c) => [(string) $c->value => $c->getLabel()])->toArray()
                            + ['gold' => 'Gold']
                    ),
                Filter::make('created_at')
                    ->schema([
                        \Filament\Forms\Components\DatePicker::make('from')->label(__('filament/webmall.filter_from')),
                        \Filament\Forms\Components\DatePicker::make('until')->label(__('filament/webmall.filter_until')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'], fn($q, $v) => $q->whereDate('created_at', '>=', $v))
                            ->when($data['until'], fn($q, $v) => $q->whereDate('created_at', '<=', $v));
                    }),
            ])
            ->headerActions([])
            ->toolbarActions([])
            ->emptyStateIcon('heroicon-o-shopping-bag')
            ->emptyStateHeading(__('filament/webmall.empty_user_purchases_heading'))
            ->emptyStateDescription(__('filament/webmall.empty_user_purchases_desc'));
    }
}
