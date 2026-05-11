<?php

namespace App\Filament\Resources\Webmall\Pages;

use App\Filament\Resources\Webmall\WebmallResource;
use App\Filament\Widgets\WebmallRevenueChartWidget;
use App\Filament\Widgets\WebmallStatsOverviewWidget;
use App\Filament\Widgets\WebmallTopItemsWidget;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListWebmallCategories extends ListRecords
{
    protected static string $resource = WebmallResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getHeaderWidgetsColumns(): int | array
    {
        return [
            'default' => 1,
            'xl'      => 5,
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            WebmallRevenueChartWidget::class,
            WebmallStatsOverviewWidget::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            WebmallTopItemsWidget::class,
        ];
    }
}
