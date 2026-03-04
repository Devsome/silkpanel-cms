<?php

namespace App\Filament\Resources\Users\RelationManagers;

use App\Enums\SilkTypeIsroEnum;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SkSilkHistoryRelationManager extends RelationManager
{
    protected static string $relationship = 'getSkSilkHistory';

    protected static ?string $title = 'Silk History';

    public function table(Table $table): Table
    {
        return $table
            ->columns($this->getColumnsByVersion())
            ->emptyStateIcon('heroicon-o-banknotes')
            ->emptyStateHeading(__('filament/silk.empty'))
            ->emptyStateDescription(__('filament/silk.empty_description'))
            ->headerActions([])
            ->toolbarActions([])
            ->defaultSort($this->getDefaultSortColumn(), 'desc');
    }

    private function getColumnsByVersion(): array
    {
        return config('silkpanel.version') === 'isro'
            ? $this->getIsroColumns()
            : $this->getVsroColumns();
    }

    private function getIsroColumns(): array
    {
        return [
            TextColumn::make('CSID')
                ->label(__('filament/silk.table.csid')),
            TextColumn::make('PTInvoiceID')
                ->label(__('filament/silk.table.pt_invoice_id')),
            TextColumn::make('ChangedSilk')
                ->label(__('filament/silk.table.changed_silk')),
            TextColumn::make('RemainedSilk')
                ->label(__('filament/silk.table.remained_silk')),
            TextColumn::make('SilkType')
                ->label(__('filament/silk.table.silk_type'))
                ->formatStateUsing(fn($state) => SilkTypeIsroEnum::tryFrom((int) $state)?->getLabel() ?? (string) $state)
                ->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('SellingTypeID')
                ->label(__('filament/silk.table.selling_type_id'))
                ->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('ChangeDate')
                ->label(__('filament/silk.table.change_date'))
                ->toggleable(isToggledHiddenByDefault: true),
        ];
    }

    private function getVsroColumns(): array
    {
        return [
            TextColumn::make('Silk_Offset')
                ->label(__('filament/silk.table.silk_offset')),
            TextColumn::make('BuyQuantity')
                ->label(__('filament/silk.table.buy_quantity')),
            TextColumn::make('Silk_Remain')
                ->label(__('filament/silk.table.silk_remain')),
            TextColumn::make('AuthDate')
                ->label(__('filament/silk.table.auth_date'))
                ->sortable(),
            TextColumn::make('user.name')
                ->label(__('filament/silk.table.sub_jid'))
                ->description(fn($record): string => $record->user->email)
                ->toggleable(isToggledHiddenByDefault: false),
            TextColumn::make('SlipPaper')
                ->label(__('filament/silk.table.slip_paper'))
                ->toggleable(isToggledHiddenByDefault: false),
            TextColumn::make('IP')
                ->label(__('filament/silk.table.ip'))
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ];
    }

    private function getDefaultSortColumn(): string
    {
        return config('silkpanel.version') === 'isro' ? 'ChangeDate' : 'AuthDate';
    }
}
