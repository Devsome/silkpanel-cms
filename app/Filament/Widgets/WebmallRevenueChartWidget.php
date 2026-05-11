<?php

namespace App\Filament\Widgets;

use App\Models\WebmallPurchase;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class WebmallRevenueChartWidget extends ChartWidget
{
    protected ?string $heading = null;

    public function getHeading(): string
    {
        return __('filament/webmall.chart_daily_revenue');
    }

    protected ?string $maxHeight = '200px';

    protected int | string | array $columnSpan = [
        'default' => 'full',
        'xl'      => 3,
    ];

    protected function getData(): array
    {
        $days = collect(range(13, 0))->map(fn($i) => now()->subDays($i)->toDateString());

        $purchases = WebmallPurchase::query()
            ->where('created_at', '>=', now()->subDays(14)->startOfDay())
            ->get();

        $silkByDay  = $days->mapWithKeys(fn($d) => [$d => 0]);
        $goldByDay  = $days->mapWithKeys(fn($d) => [$d => 0]);

        foreach ($purchases as $p) {
            $day = Carbon::parse($p->created_at)->toDateString();
            if (!$silkByDay->has($day)) {
                continue;
            }
            if ($p->price_type === 'gold') {
                $goldByDay[$day] += $p->price_value;
            } else {
                $silkByDay[$day] += $p->price_value;
            }
        }

        return [
            'datasets' => [
                [
                    'label'           => __('filament/webmall.chart_silk_revenue'),
                    'data'            => $silkByDay->values()->toArray(),
                    'borderColor'     => '#6366f1',
                    'backgroundColor' => 'rgba(99,102,241,0.15)',
                    'fill'            => true,
                ],
                [
                    'label'           => __('filament/webmall.chart_gold_revenue'),
                    'data'            => $goldByDay->values()->toArray(),
                    'borderColor'     => '#f59e0b',
                    'backgroundColor' => 'rgba(245,158,11,0.15)',
                    'fill'            => true,
                ],
            ],
            'labels' => $days->values()->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
