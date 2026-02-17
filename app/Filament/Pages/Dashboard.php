<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    /**
     * Get the columns for the dashboard grid
     * 
     * @return int|string|array
     */
    public function getColumns(): int | array
    {
        return [
            'default' => 1,
            'sm' => 2,
            'md' => 2,
            'lg' => 5,
            'xl' => 5,
            '2xl' => 5,
        ];
    }

    /**
     * Get the widgets to display on the dashboard
     * 
     * @return array
     */
    public function getWidgets(): array
    {
        return [
            \SilkPanel\WidgetsDashboard\Widgets\StatsOverviewWidget::class,
            \SilkPanel\WidgetsDashboard\Widgets\PackageUpdateWidget::class,
            \SilkPanel\WidgetsDashboard\Widgets\LicenseOverviewWidget::class,
        ];
    }
}
