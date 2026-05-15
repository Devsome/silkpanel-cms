<?php

namespace App\Filament\Resources\SessionModals;

use App\Filament\Resources\SessionModals\Pages\CreateSessionModal;
use App\Filament\Resources\SessionModals\Pages\EditSessionModal;
use App\Filament\Resources\SessionModals\Pages\ListSessionModals;
use App\Models\SessionModal;
use BackedEnum;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Route as LaravelRoute;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SessionModalResource extends Resource
{
    protected static ?string $model = SessionModal::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|\UnitEnum|null $navigationGroup = 'Administration';

    protected static ?int $navigationSort = 8;

    public static function getNavigationLabel(): string
    {
        return __('filament/session-modals.navigation_label');
    }

    public static function getModelLabel(): string
    {
        return __('filament/session-modals.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament/session-modals.plural_model_label');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('filament/session-modals.section.content'))
                ->description(__('filament/session-modals.section.content_description'))
                ->schema([
                    TextInput::make('title')
                        ->label(__('filament/session-modals.form.title'))
                        ->helperText(__('filament/session-modals.form.title_helper'))
                        ->maxLength(255),

                    RichEditor::make('content')
                        ->label(__('filament/session-modals.form.content'))
                        ->required()
                        ->fileAttachmentsVisibility('public')
                        ->toolbarButtons([
                            'heading' => [
                                'alignStart',
                                'alignCenter',
                                'alignEnd'
                            ],
                            'paragraph' => [
                                'bold',
                                'italic',
                                'underline',
                                'strike',
                            ],
                            'image' => [
                                'attachFiles',
                            ],
                        ])
                        ->columnSpanFull(),

                    FileUpload::make('image')
                        ->label(__('filament/session-modals.form.image'))
                        ->helperText(__('filament/session-modals.form.image_helper'))
                        ->image()
                        ->imageEditor()
                        ->maxSize(5120)
                        ->directory('session-modals')
                        ->visibility('public')
                        ->columnSpanFull(),

                    Repeater::make('buttons')
                        ->label(__('filament/session-modals.form.buttons'))
                        ->schema([
                            TextInput::make('label')
                                ->label(__('filament/session-modals.form.button_label'))
                                ->required()
                                ->maxLength(100),
                            Select::make('style')
                                ->label(__('filament/session-modals.form.button_style'))
                                ->options([
                                    'primary' => __('filament/session-modals.form.button_style_primary'),
                                    'secondary' => __('filament/session-modals.form.button_style_secondary'),
                                    'danger' => __('filament/session-modals.form.button_style_danger'),
                                ])
                                ->default('primary')
                                ->required(),
                            TextInput::make('url')
                                ->label(__('filament/session-modals.form.button_url'))
                                ->helperText(__('filament/session-modals.form.button_url_helper'))
                                ->url()
                                ->maxLength(500),
                            Toggle::make('target_blank')
                                ->label(__('filament/session-modals.form.button_target_blank'))
                                ->helperText(__('filament/session-modals.form.button_target_blank_helper'))
                                ->inline(false)
                                ->default(false),

                        ])
                        ->columns(2)
                        ->maxItems(4)
                        ->addActionLabel(__('filament/session-modals.form.add_button'))
                        ->columnSpanFull(),
                ])
                ->columns(2),

            Section::make(__('filament/session-modals.section.conditions'))
                ->description(__('filament/session-modals.section.conditions_description'))
                ->schema([
                    Select::make('frequency')
                        ->label(__('filament/session-modals.form.frequency'))
                        ->options([
                            'once_per_session' => __('filament/session-modals.form.frequency_once_per_session'),
                            'once_per_day' => __('filament/session-modals.form.frequency_once_per_day'),
                            'once_per_user' => __('filament/session-modals.form.frequency_once_per_user'),
                            'always' => __('filament/session-modals.form.frequency_always'),
                        ])
                        ->default('once_per_session')
                        ->live()
                        ->helperText(
                            fn(Get $get): ?string =>
                            $get('frequency') === 'once_per_user'
                                ? __('filament/session-modals.form.frequency_once_per_user_guest_hint')
                                : null
                        )
                        ->required(),

                    Select::make('conditions.audience')
                        ->label(__('filament/session-modals.form.conditions_audience'))
                        ->options([
                            'all'            => __('filament/session-modals.form.conditions_audience_all'),
                            'guests_only'    => __('filament/session-modals.form.conditions_audience_guests_only'),
                            'logged_in_only' => __('filament/session-modals.form.conditions_audience_logged_in_only'),
                        ])
                        ->default('all')
                        ->required(),

                    TextInput::make('conditions.min_character_level')
                        ->label(__('filament/session-modals.form.conditions_min_character_level'))
                        ->helperText(__('filament/session-modals.form.conditions_min_character_level_helper'))
                        ->numeric()
                        ->minValue(1)
                        ->columnStart(1),

                    TextInput::make('conditions.new_players_days')
                        ->label(__('filament/session-modals.form.conditions_new_players_days'))
                        ->helperText(__('filament/session-modals.form.conditions_new_players_days_helper'))
                        ->numeric()
                        ->minValue(1)
                        ->default(7)
                        ->visible(fn($get) => (bool) $get('conditions.new_players_only')),


                    Toggle::make('conditions.not_voted_today')
                        ->columnStart(1)
                        ->label(__('filament/session-modals.form.conditions_not_voted_today')),

                    Toggle::make('conditions.new_players_only')
                        ->label(__('filament/session-modals.form.conditions_new_players_only'))
                        ->reactive(),

                    Select::make('conditions.pages')
                        ->label(__('filament/session-modals.form.conditions_pages'))
                        ->helperText(__('filament/session-modals.form.conditions_pages_helper'))
                        ->options(function () {
                            $excluded = ['filament.', 'livewire.', 'ignition.', 'debugbar.', 'installer.', 'api.', 'admin.'];
                            $excludedExact = [
                                'language.switch',
                                'languages.switch',
                                'password.confirm',
                                'password.request',
                                'password.reset',
                                'storage.local',
                                'template.preview-image',
                                'verification.notice',
                                'verification.verify',
                            ];

                            return collect(LaravelRoute::getRoutes())
                                ->filter(fn($route) => $route->getName() !== null)
                                ->filter(fn($route) => in_array('GET', $route->methods()))
                                ->filter(fn($route) => !in_array($route->getName(), $excludedExact))
                                ->filter(function ($route) use ($excluded) {
                                    foreach ($excluded as $prefix) {
                                        if (str_starts_with($route->getName(), $prefix)) {
                                            return false;
                                        }
                                    }
                                    return true;
                                })
                                ->mapWithKeys(fn($route) => [
                                    $route->getName() => $route->getName() . '  [/' . ltrim($route->uri(), '/') . ']',
                                ])
                                ->sort()
                                ->toArray();
                        })
                        ->multiple()
                        ->searchable()
                        ->columnSpanFull(),

                    Section::make(__('filament/session-modals.section.scheduling'))
                        ->description(__('filament/session-modals.section.scheduling_description'))
                        ->schema([
                            DateTimePicker::make('starts_at')
                                ->label(__('filament/session-modals.form.starts_at')),
                            DateTimePicker::make('ends_at')
                                ->label(__('filament/session-modals.form.ends_at')),
                        ])
                        ->columns(2)
                        ->columnSpanFull(),

                    Section::make(__('filament/session-modals.section.settings'))
                        ->schema([
                            Toggle::make('is_active')
                                ->label(__('filament/session-modals.form.is_active'))
                                ->helperText(__('filament/session-modals.form.is_active_helper'))
                                ->default(true)
                                ->columnSpan(2),
                            Toggle::make('allow_backdrop_dismiss')
                                ->label(__('filament/session-modals.form.allow_backdrop_dismiss'))
                                ->default(true)
                                ->columnSpan(2),
                            TextInput::make('sort_order')
                                ->label(__('filament/session-modals.form.sort_order'))
                                ->helperText(__('filament/session-modals.form.sort_order_helper'))
                                ->numeric()
                                ->default(0)
                                ->columnSpan(4)
                                ->columnStart(1),
                        ])
                        ->columns(4)
                        ->columnSpanFull(),
                ])
                ->columns(2),

        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label(__('filament/session-modals.table.title'))
                    ->placeholder('—')
                    ->searchable()
                    ->limit(40),
                IconColumn::make('is_active')
                    ->label(__('filament/session-modals.table.is_active'))
                    ->boolean(),
                TextColumn::make('frequency')
                    ->label(__('filament/session-modals.table.frequency'))
                    ->formatStateUsing(fn(string $state): string => __("filament/session-modals.form.frequency_{$state}")),
                TextColumn::make('starts_at')
                    ->label(__('filament/session-modals.table.starts_at'))
                    ->dateTime()
                    ->placeholder('—')
                    ->sortable(),
                TextColumn::make('ends_at')
                    ->label(__('filament/session-modals.table.ends_at'))
                    ->dateTime()
                    ->placeholder('—')
                    ->sortable(),
                TextColumn::make('sort_order')
                    ->label(__('filament/session-modals.table.sort_order'))
                    ->sortable(),
            ])
            ->defaultSort('sort_order')
            ->emptyStateHeading(__('filament/session-modals.table.empty'))
            ->emptyStateDescription(__('filament/session-modals.table.empty_description'))
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->reorderable('sort_order');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSessionModals::route('/'),
            'create' => CreateSessionModal::route('/create'),
            'edit' => EditSessionModal::route('/{record}/edit'),
        ];
    }
}
