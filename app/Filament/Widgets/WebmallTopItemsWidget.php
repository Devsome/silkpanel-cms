<?php

namespace App\Filament\Widgets;

use App\Models\WebmallPurchase;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\DB;

class WebmallTopItemsWidget extends BaseWidget
{
    protected static ?string $heading = 'Most Sold Items (Last 7 Days)';

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                WebmallPurchase::query()
                    ->select('item_name', DB::raw('COUNT(*) as total_sold'))
                    ->where('created_at', '>=', now()->subDays(7))
                    ->groupBy('item_name')
                    ->orderByDesc('total_sold')
                    ->limit(10)
            )
            ->recordTitleAttribute('item_name')
            ->columns([
                TextColumn::make('item_name')
                    ->label('Item'),
                TextColumn::make('total_sold')
                    ->label('Sold (7d)')
                    ->badge()
                    ->color('success'),
            ])
            ->defaultSort('total_sold', 'desc')
            ->paginated(false);
    }

    public function getTableRecordKey(\Illuminate\Database\Eloquent\Model | array $record): string
    {
        if (is_array($record)) {
            return (string) ($record['item_name'] ?? '');
        }

        return (string) $record->item_name;
    }
}
