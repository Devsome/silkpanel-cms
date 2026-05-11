<?php

namespace App\Filament\Resources\Webmall;

use App\Filament\Resources\Webmall\Pages\CreateWebmallCategory;
use App\Filament\Resources\Webmall\Pages\EditWebmallCategory;
use App\Filament\Resources\Webmall\Pages\ListWebmallCategories;
use App\Filament\Resources\Webmall\RelationManagers\ItemsRelationManager;
use App\Filament\Resources\Webmall\RelationManagers\PurchasesRelationManager;
use App\Models\WebmallCategory;
use BackedEnum;
use Filament\Actions\ActionGroup;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\CheckboxList;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class WebmallResource extends Resource
{
    protected static ?string $model = WebmallCategory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShoppingCart;

    protected static string|\UnitEnum|null $navigationGroup = 'Webmall';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 30;

    public static function getNavigationLabel(): string
    {
        return __('filament/webmall.navigation_categories');
    }

    public static function getModelLabel(): string
    {
        return __('filament/webmall.category_model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament/webmall.category_model_label_plural');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('filament/webmall.section_category_details'))
                ->schema([
                    TextInput::make('name')
                        ->label(__('filament/webmall.field_name'))
                        ->required()
                        ->maxLength(100)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn($state, $set) => $set('slug', Str::slug($state))),

                    TextInput::make('slug')
                        ->label(__('filament/webmall.field_slug'))
                        ->required()
                        ->readOnly()
                        ->maxLength(100)
                        ->unique(ignoreRecord: true),

                    TextInput::make('order')
                        ->label(__('filament/webmall.field_order'))
                        ->numeric()
                        ->default(0)
                        ->minValue(0),

                    Toggle::make('enabled')
                        ->label(__('filament/webmall.field_enabled'))
                        ->inline(false)
                        ->onColor('primary')
                        ->default(false),
                ])->columns(4)
                ->columnSpanFull(),

            Section::make(__('filament/webmall.section_availability'))
                ->description(fn() => __('filament/webmall.section_availability_desc', ['time' => now()->format('d.m.Y H:i:s'), 'tz' => now()->timezoneName]))
                ->schema([
                    DateTimePicker::make('available_from')
                        ->label(__('filament/webmall.field_available_from'))
                        ->seconds(false)
                        ->nullable()
                        ->native(false)
                        ->suffixAction(
                            Action::make('clear_available_from')
                                ->icon('heroicon-m-x-mark')
                                ->color('gray')
                                ->tooltip(__('filament/webmall.tooltip_clear_date'))
                                ->visible(fn($get) => filled($get('available_from')))
                                ->action(fn($set) => $set('available_from', null))
                        ),

                    DateTimePicker::make('available_until')
                        ->label(__('filament/webmall.field_available_until'))
                        ->seconds(false)
                        ->nullable()
                        ->native(false)
                        ->afterOrEqual('available_from')
                        ->suffixAction(
                            Action::make('clear_available_until')
                                ->icon('heroicon-m-x-mark')
                                ->color('gray')
                                ->tooltip(__('filament/webmall.tooltip_clear_date'))
                                ->visible(fn($get) => filled($get('available_until')))
                                ->action(fn($set) => $set('available_until', null))
                        ),

                    CheckboxList::make('schedule_days')
                        ->label(__('filament/webmall.field_schedule_days'))
                        ->hint(__('filament/webmall.field_schedule_days_hint'))
                        ->options([
                            '1' => __('filament/webmall.day_monday'),
                            '2' => __('filament/webmall.day_tuesday'),
                            '3' => __('filament/webmall.day_wednesday'),
                            '4' => __('filament/webmall.day_thursday'),
                            '5' => __('filament/webmall.day_friday'),
                            '6' => __('filament/webmall.day_saturday'),
                            '7' => __('filament/webmall.day_sunday'),
                        ])
                        ->columns(7)
                        ->gridDirection('row')
                        ->bulkToggleable()
                        ->columnSpanFull(),
                ])->columns(2)
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label(__('filament/webmall.col_id'))
                    ->sortable(),
                TextColumn::make('name')
                    ->label(__('filament/webmall.col_name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slug')
                    ->label(__('filament/webmall.col_slug'))
                    ->searchable(),
                TextColumn::make('items_count')
                    ->label(__('filament/webmall.col_items'))
                    ->counts('items')
                    ->badge()
                    ->color('info'),
                TextColumn::make('order')
                    ->label(__('filament/webmall.col_order'))
                    ->sortable(),
                IconColumn::make('enabled')
                    ->label(__('filament/webmall.col_enabled'))
                    ->boolean(),
                TextColumn::make('available_from')
                    ->label(__('filament/webmall.col_from'))
                    ->dateTime('d.m.Y H:i')
                    ->placeholder(__('filament/webmall.placeholder_always'))
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('available_until')
                    ->label(__('filament/webmall.col_until'))
                    ->dateTime('d.m.Y H:i')
                    ->placeholder(__('filament/webmall.placeholder_always'))
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('schedule')
                    ->label(__('filament/webmall.col_schedule'))
                    ->getStateUsing(function (WebmallCategory $record): string {
                        $days = $record->schedule_days ?? [];
                        if (empty($days)) {
                            return __('filament/webmall.schedule_every_day');
                        }
                        $map = [
                            '1' => __('filament/webmall.day_mon'),
                            '2' => __('filament/webmall.day_tue'),
                            '3' => __('filament/webmall.day_wed'),
                            '4' => __('filament/webmall.day_thu'),
                            '5' => __('filament/webmall.day_fri'),
                            '6' => __('filament/webmall.day_sat'),
                            '7' => __('filament/webmall.day_sun'),
                        ];
                        $sorted = array_map('strval', $days);
                        sort($sorted);
                        return implode(', ', array_map(fn($d) => $map[$d] ?? $d, $sorted));
                    })
                    ->badge()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label(__('filament/webmall.col_created'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('order', 'asc')
            ->reorderable('order')
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->emptyStateIcon('heroicon-o-shopping-cart')
            ->emptyStateHeading(__('filament/webmall.empty_categories_heading'))
            ->emptyStateDescription(__('filament/webmall.empty_categories_desc'));
    }

    public static function getRelations(): array
    {
        return [
            ItemsRelationManager::class,
            PurchasesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListWebmallCategories::route('/'),
            'create' => CreateWebmallCategory::route('/create'),
            'edit'   => EditWebmallCategory::route('/{record}/edit'),
        ];
    }
}
