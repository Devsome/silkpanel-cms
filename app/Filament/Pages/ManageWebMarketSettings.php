<?php

namespace App\Filament\Pages;

use App\Enums\MarketFeeTypeEnum;
use App\Enums\SilkTypeEnum;
use App\Enums\SilkTypeIsroEnum;
use App\Models\Setting;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions as ActionsComponent;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

/**
 * @property-read Schema $form
 */
class ManageWebMarketSettings extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShoppingBag;

    protected string $view = 'filament.pages.manage-web-market-settings';

    protected static string|\UnitEnum|null $navigationGroup = 'Player Web Market';

    protected static ?int $navigationSort = 10;

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    public static function getNavigationLabel(): string
    {
        return 'Web Market Settings';
    }

    public function mount(): void
    {
        $this->form->fill($this->loadSettings());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Form::make([
                    Tabs::make('tabs')
                        ->columnSpanFull()
                        ->tabs([
                            Tab::make('General')
                                ->icon('heroicon-o-cog')
                                ->schema([
                                    Section::make('Module Activation')
                                        ->schema([
                                            Toggle::make('web_market_enabled')
                                                ->label('Enable Web Market')
                                                ->helperText('Master switch for the entire Web Market module.'),

                                            Toggle::make('web_storage_enabled')
                                                ->label('Enable Web Storage')
                                                ->helperText('Allow players to transfer items to/from Web Storage.'),

                                            Toggle::make('marketplace_enabled')
                                                ->label('Enable Marketplace')
                                                ->helperText('Allow players to list and buy items.'),
                                        ])->columns(1)->secondary(),

                                    Section::make('Character Status')
                                        ->schema([
                                            Toggle::make('web_market_require_logout')
                                                ->label('Require Character to be Offline')
                                                ->helperText('When enabled, the character must be logged out for all transfer and listing operations.'),
                                        ])->secondary(),
                                ])->columns(1),

                            Tab::make('Pricing & Fees')
                                ->icon('heroicon-o-currency-dollar')
                                ->schema([
                                    Section::make('Currency Settings')
                                        ->schema([
                                            Toggle::make('web_market_allow_gold')
                                                ->label('Allow Gold Sales')
                                                ->default(true),

                                            Toggle::make('web_market_allow_silk')
                                                ->label('Allow Silk Sales')
                                                ->default(true),

                                            TextInput::make('web_market_max_gold_price')
                                                ->label('Maximum Gold Price')
                                                ->helperText('Leave empty for no limit.')
                                                ->numeric()
                                                ->minValue(1),

                                            TextInput::make('web_market_max_silk_price')
                                                ->label('Maximum Silk Price')
                                                ->helperText('Leave empty for no limit.')
                                                ->numeric()
                                                ->minValue(1),

                                            Select::make('web_market_silk_type')
                                                ->label('Silk Type for Sales')
                                                ->helperText('Which silk type players can use when listing items for silk.')
                                                ->options(fn() => match (config('silkpanel.version')) {
                                                    'isro' => collect(SilkTypeIsroEnum::cases())->mapWithKeys(fn($c) => [(string) $c->value => $c->getLabel()])->all(),
                                                    default => collect(SilkTypeEnum::cases())->mapWithKeys(fn($c) => [$c->value => $c->getLabel()])->all(),
                                                })
                                                ->nullable()
                                                ->placeholder('Any silk type'),
                                        ])->columns(2)->secondary(),

                                    Section::make('Transaction Fees')
                                        ->description('Fees are deducted from the seller\'s payout at time of purchase. Leave the fee type empty to disable fees.')
                                        ->schema([
                                            Select::make('web_market_fee_type')
                                                ->label('Fee Type')
                                                ->options(MarketFeeTypeEnum::class)
                                                ->placeholder('No fees')
                                                ->nullable(),

                                            TextInput::make('web_market_fee_value')
                                                ->label('Fee Value')
                                                ->helperText('Percentage (e.g. 5 for 5%) or fixed amount.')
                                                ->numeric()
                                                ->minValue(0)
                                                ->step(0.01),
                                        ])->columns(2)->secondary(),
                                ])->columns(2),

                            Tab::make('Durations')
                                ->icon('heroicon-o-clock')
                                ->schema([
                                    Section::make('Listing Duration')
                                        ->schema([
                                            TextInput::make('web_market_default_duration_hours')
                                                ->label('Default Duration (hours)')
                                                ->helperText('Pre-filled when creating a listing. E.g. 24 = 1 day.')
                                                ->numeric()
                                                ->minValue(1)
                                                ->default(24),

                                            TextInput::make('web_market_max_duration_hours')
                                                ->label('Maximum Duration (hours)')
                                                ->helperText('Maximum duration a player can select. E.g. 336 = 14 days.')
                                                ->numeric()
                                                ->minValue(1)
                                                ->default(336),
                                        ])->columns(2)->secondary()->columnSpanFull(),
                                ])->columns(2),

                            Tab::make('Limits')
                                ->icon('heroicon-o-chart-bar')
                                ->schema([
                                    Section::make('Listing Limits')
                                        ->schema([
                                            TextInput::make('web_market_max_listings_account')
                                                ->label('Max Active Listings per Account')
                                                ->numeric()
                                                ->minValue(1)
                                                ->default(20),

                                            TextInput::make('web_market_max_listings_character')
                                                ->label('Max Active Listings per Character')
                                                ->numeric()
                                                ->minValue(1)
                                                ->default(10),

                                            TextInput::make('web_market_max_storage_items')
                                                ->label('Max Items in Web Storage per Account')
                                                ->numeric()
                                                ->minValue(1)
                                                ->default(50),
                                        ])->columns(2)->secondary()->columnSpanFull(),

                                ])->columns(2),

                        ]),
                ])
                    ->livewireSubmitHandler('save')
                    ->footer([
                        ActionsComponent::make([
                            Action::make('save')
                                ->label('Save Settings')
                                ->submit('save')
                                ->keyBindings(['mod+s']),
                        ]),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $keys = [
            'web_market_enabled',
            'web_storage_enabled',
            'marketplace_enabled',
            'web_market_require_logout',
            'web_market_allow_gold',
            'web_market_allow_silk',
            'web_market_max_gold_price',
            'web_market_max_silk_price',
            'web_market_silk_type',
            'web_market_fee_type',
            'web_market_fee_value',
            'web_market_default_duration_hours',
            'web_market_max_duration_hours',
            'web_market_max_listings_account',
            'web_market_max_listings_character',
            'web_market_max_storage_items',
        ];

        foreach ($keys as $key) {
            Setting::set($key, $data[$key] ?? null);
        }

        Notification::make()
            ->success()
            ->title('Web Market settings saved.')
            ->send();
    }

    private function loadSettings(): array
    {
        $settings = Setting::getAllSettings();

        // Normalize legacy array value stored when this field used ->multiple()
        if (isset($settings['web_market_silk_type']) && is_array($settings['web_market_silk_type'])) {
            $settings['web_market_silk_type'] = $settings['web_market_silk_type'][0] ?? null;
        }

        return $settings;
    }
}
