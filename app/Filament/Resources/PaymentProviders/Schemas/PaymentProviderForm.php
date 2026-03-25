<?php

namespace App\Filament\Resources\PaymentProviders\Schemas;

use App\Enums\PaymentProviderEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PaymentProviderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('filament/donations.provider_section_settings'))
                    ->description(__('filament/donations.provider_section_settings_description'))
                    ->schema([
                        Select::make('slug')
                            ->label(__('filament/donations.provider_field_provider'))
                            ->options(PaymentProviderEnum::class)
                            ->disabled()
                            ->required(),

                        TextInput::make('name')
                            ->label(__('filament/donations.provider_field_display_name'))
                            ->required()
                            ->maxLength(100),

                        Textarea::make('description')
                            ->label(__('filament/donations.provider_field_description'))
                            ->rows(3)
                            ->columnSpanFull(),

                        Toggle::make('is_active')
                            ->label(__('filament/donations.provider_field_active'))
                            ->helperText(__('filament/donations.provider_field_active_helper')),

                        TextInput::make('sort_order')
                            ->label(__('filament/donations.provider_field_sort_order'))
                            ->numeric()
                            ->default(0)
                            ->minValue(0),
                    ])->columns(2),

                Section::make(__('filament/donations.provider_section_assigned_packages'))
                    ->description(__('filament/donations.provider_section_assigned_packages_description'))
                    ->schema([
                        Select::make('donationPackages')
                            ->label(__('filament/donations.provider_field_packages'))
                            ->relationship('donationPackages', 'name')
                            ->multiple()
                            ->preload(),
                    ])
                    ->visible(fn($record) => $record?->slug !== PaymentProviderEnum::HIPOCARD),

                Section::make(__('filament/donations.hipocard_denomination_section'))
                    ->description(__('filament/donations.hipocard_denomination_section_description'))
                    ->schema(array_map(
                        fn(int $denom) => TextInput::make("denomination_silks.{$denom}")
                            ->label("Card Amount {$denom}")
                            ->numeric()
                            ->minValue(0)
                            ->nullable()
                            ->placeholder(fn() => $denom),
                        [5, 10, 25, 50, 75, 100, 250, 500, 1000, 2500, 5000, 10000, 25000, 50000]
                    ))
                    ->columns(3)
                    ->collapsed()
                    ->visible(fn($record) => $record?->slug === PaymentProviderEnum::HIPOCARD),

                Section::make(__('filament/donations.provider_section_api_config'))
                    ->description(__('filament/donations.provider_section_api_config_description'))
                    ->schema(fn($record) => self::getProviderConfigFields($record?->slug))
                    ->collapsed()
                    ->columnSpanFull(),

            ]);
    }

    private static function getProviderConfigFields(?PaymentProviderEnum $slug): array
    {
        if (!$slug) {
            return [];
        }

        return match ($slug) {
            PaymentProviderEnum::PAYPAL => [
                TextInput::make('env_paypal_client_id')
                    ->label('PAYPAL_CLIENT_ID')
                    ->placeholder(fn() => self::maskValue(config('donation.providers.paypal.client_id')))
                    ->default(fn() => self::maskValue(config('donation.providers.paypal.client_id')))
                    ->readOnly(true)
                    ->dehydrated(false),
                TextInput::make('env_paypal_mode')
                    ->label('PAYPAL_MODE')
                    ->placeholder(fn() => config('donation.providers.paypal.mode', 'sandbox'))
                    ->default(fn() => config('donation.providers.paypal.mode', 'sandbox'))
                    ->readOnly(true)
                    ->dehydrated(false),
            ],
            PaymentProviderEnum::STRIPE => [
                TextInput::make('env_stripe_key')
                    ->label('STRIPE_KEY')
                    ->placeholder(fn() => self::maskValue(config('donation.providers.stripe.key')))
                    ->default(fn() => self::maskValue(config('donation.providers.stripe.key')))
                    ->readOnly(true)
                    ->dehydrated(false),
                TextInput::make('env_stripe_webhook_secret')
                    ->label('STRIPE_WEBHOOK_SECRET')
                    ->placeholder(fn() => self::maskValue(config('donation.providers.stripe.webhook_secret')))
                    ->default(fn() => self::maskValue(config('donation.providers.stripe.webhook_secret')))
                    ->readOnly(true)
                    ->dehydrated(false),
            ],
            PaymentProviderEnum::HIPOPAY => [
                TextInput::make('env_hipopay_api_key')
                    ->label('HIPOPAY_API_KEY')
                    ->placeholder(fn() => self::maskValue(config('donation.providers.hipopay.api_key')))
                    ->default(fn() => self::maskValue(config('donation.providers.hipopay.api_key')))
                    ->readOnly(true)
                    ->dehydrated(false),
                TextInput::make('env_hipopay_api_secret')
                    ->label('HIPOPAY_API_SECRET')
                    ->placeholder(fn() => self::maskValue(config('donation.providers.hipopay.api_secret')))
                    ->default(fn() => self::maskValue(config('donation.providers.hipopay.api_secret')))
                    ->readOnly(true)
                    ->dehydrated(false),
            ],
            PaymentProviderEnum::HIPOCARD => [
                TextInput::make('env_hipocard_api_key')
                    ->label('HIPOCARD_API_KEY')
                    ->placeholder(fn() => self::maskValue(config('donation.providers.hipocard.api_key')))
                    ->default(fn() => self::maskValue(config('donation.providers.hipocard.api_key')))
                    ->readOnly(true)
                    ->dehydrated(false),
                TextInput::make('env_hipocard_api_secret')
                    ->label('HIPOCARD_API_SECRET')
                    ->placeholder(fn() => self::maskValue(config('donation.providers.hipocard.api_secret')))
                    ->default(fn() => self::maskValue(config('donation.providers.hipocard.api_secret')))
                    ->readOnly(true)
                    ->dehydrated(false),
                TextInput::make('env_hipocard_silk_per_unit')
                    ->label('HIPOCARD_SILK_PER_UNIT')
                    ->placeholder(fn() => config('donation.providers.hipocard.silk_per_unit'))
                    ->default(fn() => config('donation.providers.hipocard.silk_per_unit'))
                    ->readOnly(true)
                    ->dehydrated(false),
            ],
            default => [
                TextInput::make('env_notice')
                    ->label('Notice')
                    ->placeholder(__('filament/donations.provider_env_notice'))
                    ->default(__('filament/donations.provider_env_notice'))
                    ->readOnly(true)
                    ->dehydrated(false),
            ],
        };
    }

    private static function maskValue(?string $value): string
    {
        if (empty($value)) {
            return __('filament/donations.provider_env_not_configured');
        }

        $length = strlen($value);
        if ($length <= 8) {
            return str_repeat('*', $length);
        }

        return substr($value, 0, 4) . str_repeat('*', $length - 8) . substr($value, -4);
    }
}
