<?php

namespace App\Filament\Widgets;

use App\Models\Setting;
use Filament\Widgets\ChartWidget;
use SilkPanel\SilkroadModels\Models\Shard\AbstractChar;

class PlayerLevelDistributionWidget extends ChartWidget
{
    protected static ?int $sort = 10;

    protected ?string $maxHeight = '280px';

    protected int | string | array $columnSpan = [
        'default' => 'full',
        'lg'      => 3,
        'xl'      => 3,
    ];

    public function getHeading(): string
    {
        return __('filament/characters.widget_level_distribution.heading');
    }

    public function getDescription(): ?string
    {
        return __('filament/characters.widget_level_distribution.description');
    }

    protected function getData(): array
    {
        /** @var AbstractChar $charModel */
        $charModel = app(AbstractChar::class);

        $step = 10;

        // 1. Prefer the configured level cap (sro_cap setting)
        // 2. Fall back to the actual max level in the DB
        $cap = (int) Setting::get('sro_cap', 0);
        $max = $cap > 0
            ? $cap
            : (int) $charModel->newQuery()
                ->where('CharID', '!=', 0)
                ->where('CharName16', '!=', 'dummy')
                ->max('CurLevel');

        // Round up to the next full step so the last bucket is complete
        $max = (int) (ceil($max / $step) * $step);

        // Build buckets: 1–10, 11–20, ...
        $buckets = [];
        for ($from = 1; $from <= $max; $from += $step) {
            $to = $from + $step - 1;
            $buckets[] = ['from' => $from, 'to' => $to, 'label' => "{$from}–{$to}"];
        }

        $counts = $charModel->newQuery()
            ->where('CharID', '!=', 0)
            ->where('CharName16', '!=', 'dummy')
            ->where('CurLevel', '>=', 1)
            ->selectRaw('CurLevel, COUNT(*) as cnt')
            ->groupBy('CurLevel')
            ->pluck('cnt', 'CurLevel');

        $data   = [];
        $labels = [];
        $colors = [];

        foreach ($buckets as $bucket) {
            $total = 0;
            for ($lvl = $bucket['from']; $lvl <= $bucket['to']; $lvl++) {
                $total += $counts->get($lvl, 0);
            }

            // Skip completely empty high-level buckets at the tail
            $data[]   = $total;
            $labels[] = $bucket['label'];

            // Color gradient: low levels = blue, mid = purple, high = amber
            $ratio = ($bucket['from'] - 1) / ($max - 1);
            if ($ratio < 0.5) {
                $r = (int) (99  + (124 - 99)  * ($ratio * 2));
                $g = (int) (102 + (58  - 102) * ($ratio * 2));
                $b = (int) (241 + (237 - 241) * ($ratio * 2));
            } else {
                $r = (int) (124 + (245 - 124) * (($ratio - 0.5) * 2));
                $g = (int) (58  + (158 - 58)  * (($ratio - 0.5) * 2));
                $b = (int) (237 + (11  - 237) * (($ratio - 0.5) * 2));
            }
            $colors[] = "rgba({$r},{$g},{$b},0.85)";
        }

        return [
            'datasets' => [
                [
                    'label'           => __('filament/characters.widget_level_distribution.dataset_label'),
                    'data'            => $data,
                    'backgroundColor' => $colors,
                    'borderColor'     => array_map(
                        fn($c) => str_replace('0.85', '1', $c),
                        $colors
                    ),
                    'borderWidth'     => 1,
                    'borderRadius'    => 6,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => ['display' => false],
                'tooltip' => [
                    'displayColors' => false,
                    'titlePrefix'   => 'Level ',
                ],
            ],
            'scales' => [
                'x' => [
                    'title' => [
                        'display' => true,
                        'text'    => __('filament/characters.widget_level_distribution.x_label'),
                        'color'   => 'rgb(107,114,128)',
                        'font'    => ['size' => 11],
                    ],
                    'grid' => ['display' => false],
                ],
                'y' => [
                    'title' => [
                        'display' => true,
                        'text'    => __('filament/characters.widget_level_distribution.y_label'),
                        'color'   => 'rgb(107,114,128)',
                        'font'    => ['size' => 11],
                    ],
                    'beginAtZero' => true,
                    'ticks'       => ['precision' => 0],
                ],
            ],
        ];
    }
}
