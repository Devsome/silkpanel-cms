<?php

namespace App\Filament\Resources\DonationPackages\Pages;

use App\Filament\Resources\DonationPackages\DonationPackageResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDonationPackages extends ListRecords
{
    protected static string $resource = DonationPackageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
