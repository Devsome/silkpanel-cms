<?php

namespace App\Filament\Resources\Notices;

use App\Filament\Resources\Notices\Pages\ManageNotices;
use BackedEnum;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use SilkPanel\SilkroadModels\Models\Account\Notice;
use SilkPanel\SilkroadModels\Models\Account\Shard;

class NoticeResource extends Resource
{
    protected static ?string $model = Notice::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedNewspaper;

    protected static string | \UnitEnum | null $navigationGroup = 'Silkroad';

    protected static ?string $recordTitleAttribute = 'Subject';

    protected static ?int $navigationSort = 10;

    protected static bool $canCreateAnother = false;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('Subject')
                    ->label(__('filament/notice.form.subject'))
                    ->helperText(__('filament/notice.form.subject_helper'))
                    ->required()
                    ->maxLength(80),
                DateTimePicker::make('EditDate')
                    ->label(__('filament/notice.form.edit_date'))
                    ->default(now())
                    ->required(),
                Select::make('ContentID')
                    ->label(__('filament/notice.form.block_shard'))
                    ->options(Shard::query()->pluck('szName', 'nContentID')),
                Textarea::make('Article')
                    ->label(__('filament/notice.form.article'))
                    ->helperText(__('filament/notice.form.article_helper'))
                    ->rows(5)
                    ->cols(10)
                    ->required()
                    ->maxLength(1024)
                    ->disableGrammarly()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('Subject')
            ->columns([
                TextColumn::make('ContentID')
                    ->label(__('filament/notice.table.content_id'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('Subject')
                    ->label(__('filament/notice.table.subject'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('Article')
                    ->label(__('filament/notice.table.article'))
                    ->limit(30, end: ' ...')
                    ->searchable(),
                TextColumn::make('EditDate')
                    ->label(__('filament/notice.table.edit_date'))
                    ->description(fn($record) => Carbon::parse($record->EditDate)?->diffForHumans()),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('ID', 'desc')
            ->emptyStateIcon('heroicon-o-newspaper')
            ->emptyStateHeading(__('filament/notice.table.empty'))
            ->emptyStateDescription(__('filament/notice.table.empty_description'));
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageNotices::route('/'),
        ];
    }
}
