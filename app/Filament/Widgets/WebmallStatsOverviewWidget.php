<?php

namespace App\Filament\Widgets;

use App\Models\WebmallPurchase;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class WebmallStatsOverviewWidget extends BaseWidget
{
    protected ?string $pollingInterval = null;

    protected int | string | array $columnSpan = [
        'default' => 'full',
        'xl'      => 2,
    ];

    protected function getColumns(): int
    {
        return 2;
    }

    protected function getStats(): array
    {
        $totalSales = WebmallPurchase::count();

        $totalSilkRevenue = WebmallPurchase::where('price_type', '!=', 'gold')->sum('price_value');
        $totalGoldRevenue = WebmallPurchase::where('price_type', 'gold')->sum('price_value');

        $salesToday = WebmallPurchase::whereDate('created_at', today())->count();

        return [
            Stat::make(__('filament/webmall.stat_total_purchases'), number_format($totalSales))
                ->description(__('filament/webmall.stat_total_purchases_desc'))
                ->color('success'),

            Stat::make(__('filament/webmall.stat_silk_revenue'), number_format($totalSilkRevenue))
                ->description(__('filament/webmall.stat_silk_revenue_desc'))
                ->color('info'),

            Stat::make(__('filament/webmall.stat_gold_revenue'), number_format($totalGoldRevenue))
                ->description(__('filament/webmall.stat_gold_revenue_desc'))
                ->color('warning'),

            Stat::make(__('filament/webmall.stat_sales_today'), number_format($salesToday))
                ->description(__('filament/webmall.stat_sales_today_desc'))
                ->color('primary'),
        ];
    }
}
