<?php

namespace App\Filament\Pages;

use App\Enums\Languages;
use App\Models\Setting;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Utilities\Get;
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
class ManageSettings extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected string $view = 'filament.pages.manage-settings';

    protected static string|\UnitEnum|null $navigationGroup = 'Configuration';

    protected static ?int $navigationSort = 50;

    /**
     * @var array<string, mixed> | null
     */
    public ?array $data = [];

    public static function getNavigationLabel(): string
    {
        return __('filament/settings.navigation');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament/settings.navigation_group');
    }

    public function mount(): void
    {
        $this->form->fill($this->getSettingsArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Form::make([
                    Tabs::make('tabs')
                        ->columnSpanFull()
                        ->tabs([
                            Tab::make(__('filament/settings.form.tabs.general'))
                                ->icon('heroicon-o-information-circle')
                                ->schema([
                                    TextInput::make('site_title')
                                        ->label(__('filament/settings.form.page_info.site_title'))
                                        ->placeholder(__('filament/settings.form.page_info.site_title_placeholder'))
                                        ->maxLength(255),

                                    Textarea::make('site_description')
                                        ->label(__('filament/settings.form.page_info.site_description'))
                                        ->placeholder(__('filament/settings.form.page_info.site_description_placeholder'))
                                        ->rows(3),

                                    TextInput::make('site_keywords')
                                        ->label(__('filament/settings.form.page_info.site_keywords'))
                                        ->placeholder(__('filament/settings.form.page_info.site_keywords_placeholder'))
                                        ->maxLength(255),

                                    Select::make('frontend_languages')
                                        ->label(__('filament/settings.form.page_info.frontend_languages'))
                                        ->helperText(__('filament/settings.form.page_info.frontend_languages_description'))
                                        ->multiple()
                                        ->options(
                                            Languages::class
                                        )
                                        ->searchable()
                                        ->preload()
                                        ->default(['en'])
                                        ->required(),
                                ]),

                            Tab::make(__('filament/settings.form.tabs.silkroad_online'))
                                ->icon('heroicon-o-globe-alt')
                                ->schema([
                                    Section::make(__('filament/settings.form.silkroad_online.general_settings'))
                                        ->schema([
                                            TextInput::make('sro_max_player')
                                                ->label(__('filament/settings.form.silkroad_online.max_player'))
                                                ->numeric()
                                                ->minValue(1)
                                                ->maxValue(10000)
                                                ->default(500),

                                            TextInput::make('sro_cap')
                                                ->label(__('filament/settings.form.silkroad_online.cap'))
                                                ->numeric()
                                                ->minValue(1)
                                                ->maxValue(200)
                                                ->default(110),
                                        ])->columns(2)
                                        ->columnSpan(2)
                                        ->secondary(),
                                    Section::make(__('filament/settings.form.silkroad_online.rate_settings'))
                                        ->schema([
                                            TextInput::make('sro_exp_sp')
                                                ->label(__('filament/settings.form.silkroad_online.exp_sp'))
                                                ->numeric()
                                                ->minValue(1)
                                                ->maxValue(10000)
                                                ->default(1),

                                            TextInput::make('sro_party_exp')
                                                ->label(__('filament/settings.form.silkroad_online.party_exp'))
                                                ->numeric()
                                                ->minValue(1)
                                                ->maxValue(10000)
                                                ->default(1),

                                            TextInput::make('sro_gold_drop_rate')
                                                ->label(__('filament/settings.form.silkroad_online.gold_drop_rate'))
                                                ->numeric()
                                                ->minValue(0.1)
                                                ->maxValue(1000)
                                                ->step(0.1)
                                                ->default(1.0),

                                            TextInput::make('sro_drop_rate')
                                                ->label(__('filament/settings.form.silkroad_online.drop_rate'))
                                                ->numeric()
                                                ->minValue(0.1)
                                                ->maxValue(1000)
                                                ->step(0.1)
                                                ->default(1.0),

                                            TextInput::make('sro_trade_rate')
                                                ->label(__('filament/settings.form.silkroad_online.trade_rate'))
                                                ->numeric()
                                                ->minValue(0.1)
                                                ->maxValue(1000)
                                                ->step(0.1)
                                                ->default(1.0),
                                        ])->columns(2)
                                        ->columnSpan(2)
                                        ->secondary(),
                                    Section::make(__('filament/settings.form.silkroad_online.other_settings'))
                                        ->schema([
                                            CheckboxList::make('sro_race')
                                                ->label(__('filament/settings.form.silkroad_online.race'))
                                                ->options([
                                                    'china' => 'China',
                                                    'europe' => 'Europe',
                                                ])
                                                ->default(['china', 'europe'])
                                                ->columns(2),
                                            CheckboxList::make('sro_fortress_war')
                                                ->label(__('filament/settings.form.silkroad_online.fortress_war'))
                                                ->options([
                                                    'bandit' => 'Bandit Fortress',
                                                    'hotan' => 'Hotan Fortress',
                                                    'jangan' => 'Jangan Fortress',
                                                ])
                                                ->default(['bandit', 'hotan', 'jangan'])
                                                ->columns(3),
                                        ])->columns(2)
                                        ->columnSpan(2)
                                        ->secondary(),
                                    Section::make(__('filament/settings.form.silkroad_online.ip_settings'))
                                        ->schema([
                                            TextInput::make('sro_hwid_limit')
                                                ->label(__('filament/settings.form.silkroad_online.hwid_limit'))
                                                ->helperText(__('filament/settings.form.silkroad_online.hwid_limit_description'))
                                                ->numeric()
                                                ->minValue(1)
                                                ->maxValue(10)
                                                ->default(3),

                                            TextInput::make('sro_ip_limit')
                                                ->label(__('filament/settings.form.silkroad_online.ip_limit'))
                                                ->helperText(__('filament/settings.form.silkroad_online.ip_limit_description'))
                                                ->numeric()
                                                ->minValue(1)
                                                ->maxValue(10)
                                                ->default(3),
                                        ])->columns(2)
                                        ->columnSpan(2)
                                        ->secondary(),
                                ])->columns(2),

                            Tab::make(__('filament/settings.form.tabs.design'))
                                ->icon('heroicon-o-paint-brush')
                                ->schema([
                                    FileUpload::make('logo')
                                        ->label(__('filament/settings.form.design.logo'))
                                        ->image()
                                        ->directory('settings/images')
                                        ->maxSize(5120),

                                    FileUpload::make('favicon')
                                        ->label(__('filament/settings.form.design.favicon'))
                                        ->helperText(__('filament/settings.form.design.favicon_description'))
                                        ->image()
                                        ->directory('settings/images')
                                        ->maxSize(2048)
                                        ->imageAspectRatio('1:1')
                                        ->automaticallyOpenImageEditorForAspectRatio()
                                        ->automaticallyResizeImagesToWidth(512)
                                        ->automaticallyResizeImagesToHeight(512),

                                    FileUpload::make('background_image')
                                        ->label(__('filament/settings.form.design.background_image'))
                                        ->image()
                                        ->directory('settings/images')
                                        ->maxSize(10240),
                                ])->columns(2),

                            Tab::make(__('filament/settings.form.tabs.features'))
                                ->icon('heroicon-o-rocket-launch')
                                ->schema([
                                    Toggle::make('registration_open')
                                        ->label(__('filament/settings.form.features.registration_open'))
                                        ->helperText(__('filament/settings.form.features.registration_open_description')),

                                    Toggle::make('email_verification_required')
                                        ->label(__('filament/settings.form.features.email_verification_required'))
                                        ->helperText(__('filament/settings.form.features.email_verification_required_description')),

                                    Textarea::make('maintenance_message')
                                        ->label(__('filament/settings.form.features.maintenance_message'))
                                        ->placeholder(__('filament/settings.form.features.maintenance_message_placeholder'))
                                        ->rows(4),

                                    Toggle::make('tos_enabled')
                                        ->label(__('filament/settings.form.features.tos_enabled'))
                                        ->helperText(__('filament/settings.form.features.tos_enabled_description'))
                                        ->live(),

                                    RichEditor::make('tos_text')
                                        ->label(__('filament/settings.form.features.tos_text'))
                                        ->toolbarButtons([
                                            ['bold', 'italic', 'underline', 'strike', 'link'],
                                            ['h2', 'h3'],
                                            ['bulletList', 'orderedList'],
                                            ['undo', 'redo'],
                                        ])
                                        ->columnSpanFull()
                                        ->dehydrated()
                                        ->visible(fn(Get $get) => (bool) $get('tos_enabled')),

                                    Toggle::make('login_with_name')
                                        ->label(__('filament/settings.form.features.login_with_name'))
                                        ->helperText(__('filament/settings.form.features.login_with_name_description')),
                                ]),

                            Tab::make(__('filament/settings.form.tabs.partners'))
                                ->icon('heroicon-o-users')
                                ->schema([
                                    Repeater::make('partners')
                                        ->label(__('filament/settings.form.partners.partners'))
                                        ->schema([
                                            TextInput::make('name')
                                                ->label(__('filament/settings.form.partners.partner_name'))
                                                ->required(),
                                            TextInput::make('url')
                                                ->label(__('filament/settings.form.partners.partner_url'))
                                                ->url(),
                                            FileUpload::make('logo')
                                                ->label(__('filament/settings.form.partners.partner_logo'))
                                                ->image()
                                                ->directory('settings/partners'),
                                            Textarea::make('description')
                                                ->label(__('filament/settings.form.partners.partner_description'))
                                                ->rows(3),
                                        ])
                                        ->columns(2)
                                        ->collapsible(),
                                ]),
                            Tab::make(__('filament/settings.form.tabs.contact'))
                                ->icon('heroicon-o-envelope')
                                ->schema([
                                    TextInput::make('contact_email')
                                        ->label(__('filament/settings.form.contact.contact_email'))
                                        ->email(),

                                    TextInput::make('contact_phone')
                                        ->label(__('filament/settings.form.contact.contact_phone'))
                                        ->tel(),

                                    TextInput::make('contact_address')
                                        ->label(__('filament/settings.form.contact.contact_address')),
                                ])
                                ->columns(2),

                            Tab::make(__('filament/settings.form.tabs.social_media'))
                                ->icon('heroicon-o-share')
                                ->schema([
                                    TextInput::make('social_facebook')
                                        ->label(__('filament/settings.form.social_media.social_facebook'))
                                        ->url(),

                                    TextInput::make('social_twitter')
                                        ->label(__('filament/settings.form.social_media.social_twitter'))
                                        ->url(),

                                    TextInput::make('social_instagram')
                                        ->label(__('filament/settings.form.social_media.social_instagram'))
                                        ->url(),

                                    TextInput::make('social_discord')
                                        ->label(__('filament/settings.form.social_media.social_discord'))
                                        ->url(),
                                ])
                                ->columns(2),
                        ]),
                ])
                    ->livewireSubmitHandler('save')
                    ->footer([
                        ActionsComponent::make([
                            Action::make('save')
                                ->label(__('filament/settings.actions.save'))
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

        $settingKeys = [
            'site_title',
            'site_description',
            'site_keywords',
            'frontend_languages',
            'sro_max_player',
            'sro_cap',
            'sro_exp_sp',
            'sro_party_exp',
            'sro_gold_drop_rate',
            'sro_drop_rate',
            'sro_trade_rate',
            'sro_race',
            'sro_hwid_limit',
            'sro_ip_limit',
            'sro_fortress_war',
            'logo',
            'favicon',
            'background_image',
            'registration_open',
            'email_verification_required',
            'maintenance_message',
            'contact_email',
            'contact_phone',
            'contact_address',
            'social_facebook',
            'social_twitter',
            'social_instagram',
            'social_discord',
            'partners',
            'tos_enabled',
            'login_with_name',
        ];

        foreach ($settingKeys as $key) {
            if (isset($data[$key]) && $data[$key] !== null) {
                Setting::set($key, $data[$key], null, null, null);
            }
        }

        if (array_key_exists('tos_text', $data)) {
            Setting::set('tos_text', $data['tos_text'] ?? '', null, null, null);
        }

        Notification::make()
            ->success()
            ->title(__('filament/settings.notifications.updated_title'))
            ->body(__('filament/settings.notifications.updated_message'))
            ->send();
    }

    /**
     * Get all settings as an associative array
     */
    private function getSettingsArray(): array
    {
        return Setting::getAllSettings();
    }
}
