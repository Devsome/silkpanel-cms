<?php

namespace App\Filament\Resources\DonationPackages\Schemas;

use App\Enums\SilkTypeEnum;
use App\Enums\SilkTypeIsroEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class DonationPackageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('filament/donations.package_section_details'))
                    ->schema([
                        TextInput::make('name')
                            ->label(__('filament/donations.package_field_name'))
                            ->required()
                            ->maxLength(100),

                        TextInput::make('sort_order')
                            ->label(__('filament/donations.package_field_sort_order'))
                            ->numeric()
                            ->default(0)
                            ->minValue(0),

                        Toggle::make('is_active')
                            ->label(__('filament/donations.package_field_active'))
                            ->inline(false)
                            ->offIcon(Heroicon::Power)
                            ->default(true),

                        Textarea::make('description')
                            ->label(__('filament/donations.package_field_description'))
                            ->rows(3)
                            ->columnSpanFull(),

                        FileUpload::make('image')
                            ->label(__('filament/donations.package_field_image'))
                            ->image()
                            ->imageEditor()
                            ->maxSize(2048)
                            ->directory('donation-packages')
                            ->visibility('public')
                            ->columnSpanFull(),

                    ])->columns(3),

                Section::make(__('filament/donations.package_section_pricing'))
                    ->schema([
                        TextInput::make('price')
                            ->label(__('filament/donations.package_field_price'))
                            ->required()
                            ->numeric()
                            ->minValue(0.01)
                            ->step(0.01)
                            ->prefix(fn() => config('donation.currency', 'USD')),

                        TextInput::make('currency')
                            ->label(__('filament/donations.package_field_currency'))
                            ->required()
                            ->maxLength(3)
                            ->default(fn() => config('donation.currency', 'USD')),

                        TextInput::make('silk_amount')
                            ->label(__('filament/donations.package_field_silk_amount'))
                            ->required()
                            ->numeric()
                            ->minValue(1),

                        Select::make('silk_type')
                            ->label(__('filament/donations.package_field_silk_type'))
                            ->options(fn() => match (config('silkpanel.version')) {
                                'isro' => SilkTypeIsroEnum::class,
                                default => SilkTypeEnum::class,
                            })
                            ->required(),

                        Select::make('paymentProviders')
                            ->label(__('filament/donations.package_field_payment_providers'))
                            ->relationship('paymentProviders', 'name')
                            ->multiple()
                            ->preload()
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }
}
