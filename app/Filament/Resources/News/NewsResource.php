<?php

namespace App\Filament\Resources\News;

use App\Filament\Resources\News\Pages\ManageNews;
use App\Models\News;
use BackedEnum;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class NewsResource extends Resource
{
    protected static ?string $model = News::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedNewspaper;

    protected static string | \UnitEnum | null $navigationGroup = 'Administration';

    protected static ?string $recordTitleAttribute = 'slug';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('filament/news.form.name'))
                    ->live(onBlur: false)
                    ->afterStateUpdated(fn(Set $set, ?string $state) => $set('slug', Str::slug($state)))
                    ->required(),
                TextInput::make('slug')
                    ->label(__('filament/news.form.slug'))
                    ->readOnly()
                    ->required(),
                RichEditor::make('content')
                    ->label(__('filament/news.form.content'))
                    ->required()
                    ->toolbarButtons([
                        'heading' => [
                            'h1',
                            'h2',
                            'h3',
                            'alignStart',
                            'alignCenter',
                            'alignEnd'
                        ],
                        'paragraph' => [
                            'bold',
                            'italic',
                            'underline',
                            'strike',
                            'subscript',
                            'superscript',
                        ],
                        'list' => [
                            'bulletList',
                            'orderedList',
                            'blockquote',
                            'codeBlock',
                        ],
                    ])
                    ->columnSpanFull(),
                FileUpload::make('thumbnail')
                    ->label(__('filament/news.form.thumbnail'))
                    ->helperText(__('filament/news.form.thumbnail_helper'))
                    ->image()
                    ->imageEditor()
                    ->maxSize(5120)
                    ->directory('downloads/news')
                    ->visibility('public')
                    ->columnSpanFull(),
                DateTimePicker::make('published_at')
                    ->label(__('filament/news.form.published_at'))
                    ->helperText(__('filament/news.form.published_at_helper')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('slug')
            ->columns([
                TextColumn::make('name')
                    ->label(__('filament/news.table.name'))
                    ->searchable(),
                TextColumn::make('slug')
                    ->label(__('filament/news.table.slug'))
                    ->searchable(),
                ImageColumn::make('thumbnail')
                    ->label(__('filament/news.table.thumbnail')),
                TextColumn::make('published_at')
                    ->label(__('filament/news.table.published_at'))
                    ->description(fn($record) => Carbon::parse($record->published_at)->diffForHumans())
                    ->sortable(),
                TextColumn::make('deleted_at')
                    ->label(__('filament/news.table.deleted_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make(),
                    ForceDeleteAction::make(),
                    RestoreAction::make(),
                ])
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('id', 'desc')
            ->emptyStateIcon('heroicon-o-newspaper')
            ->emptyStateHeading(__('filament/news.table.empty'))
            ->emptyStateDescription(__('filament/news.table.empty_description'));
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageNews::route('/'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
