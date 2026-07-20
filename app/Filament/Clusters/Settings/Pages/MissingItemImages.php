<?php

namespace App\Filament\Clusters\Settings\Pages;

use App\Filament\Clusters\Settings;
use App\Models\ItemImage;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions as ActionsComponent;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;
use SilkPanel\SilkroadModels\Models\Account\AbstractItemNameDesc;
use SilkPanel\SilkroadModels\Models\Shard\RefObjCommon;

class MissingItemImages extends Page implements HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static ?string $cluster = Settings::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-photo';

    protected static ?int $navigationSort = 90;

    protected string $view = 'filament.clusters.settings.missing-item-images';

    public ?array $data = [];

    public static function getNavigationLabel(): string
    {
        return __('filament/settings.form.tabs.missing_item_images');
    }

    public function getTitle(): string
    {
        return __('filament/settings.form.tabs.missing_item_images');
    }

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Form::make([
                    Select::make('ref_item_id')
                        ->label(__('filament/settings.form.missing_item_images.item'))
                        ->searchable()
                        ->required()
                        ->getSearchResultsUsing(function (string $search): array {
                            $items = RefObjCommon::where('TypeID1', 3)
                                ->where('CodeName128', 'like', "%{$search}%")
                                ->select(['ID', 'CodeName128', 'NameStrID128'])
                                ->limit(50)
                                ->get();

                            if ($items->isEmpty()) {
                                return [];
                            }

                            $nameStrIds = $items->pluck('NameStrID128')->filter()->unique()->values()->all();
                            $names = resolve(AbstractItemNameDesc::class)->getItemNames($nameStrIds);

                            return $items->mapWithKeys(function ($item) use ($names) {
                                $readableName = $names[$item->NameStrID128] ?? null;
                                $label = $readableName ? "{$readableName} ({$item->CodeName128})" : $item->CodeName128;

                                return [$item->ID => $label];
                            })->toArray();
                        })
                        ->getOptionLabelUsing(function ($value): ?string {
                            $item = RefObjCommon::select(['ID', 'CodeName128', 'NameStrID128'])->find((int) $value);
                            if (!$item) {
                                return $value;
                            }

                            $names = resolve(AbstractItemNameDesc::class)->getItemNames([$item->NameStrID128]);
                            $readableName = $names[$item->NameStrID128] ?? null;

                            return $readableName ? "{$readableName} ({$item->CodeName128})" : $item->CodeName128;
                        }),

                    FileUpload::make('image')
                        ->label(__('filament/settings.form.missing_item_images.image'))
                        ->helperText(__('filament/settings.form.missing_item_images.image_description'))
                        ->image()
                        ->directory('item-images')
                        ->required()
                        ->maxSize(2048),
                ])
                    ->columns(2)
                    ->livewireSubmitHandler('save')
                    ->footer([
                        ActionsComponent::make([
                            Action::make('save')
                                ->label(__('filament/settings.form.missing_item_images.upload'))
                                ->submit('save')
                                ->icon('heroicon-o-arrow-up-tray'),
                        ]),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $item = RefObjCommon::select(['ID', 'AssocFileIcon128'])->find((int) $data['ref_item_id']);

        if (!$item || !$item->AssocFileIcon128) {
            Notification::make()
                ->danger()
                ->title(__('filament/settings.form.missing_item_images.no_icon_path'))
                ->send();

            return;
        }

        $codename = str_replace('\\', '/', trim($item->AssocFileIcon128));
        $codename = preg_replace('/\.ddj$/i', '', $codename);
        $codename = strtolower($codename);

        if (ItemImage::where('codename', $codename)->exists()) {
            Notification::make()
                ->warning()
                ->title(__('filament/settings.form.missing_item_images.already_exists'))
                ->send();

            return;
        }

        ItemImage::create([
            'codename' => $codename,
            'image' => $data['image'],
        ]);

        $this->form->fill();

        Notification::make()
            ->success()
            ->title(__('filament/settings.form.missing_item_images.uploaded'))
            ->send();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(ItemImage::query())
            ->defaultSort('created_at', 'desc')
            ->columns([
                ImageColumn::make('image')
                    ->label(__('filament/settings.form.missing_item_images.preview'))
                    ->disk('public')
                    ->square()
                    ->size(40),

                TextColumn::make('codename')
                    ->label(__('filament/settings.form.missing_item_images.codename'))
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                TextColumn::make('created_at')
                    ->label(__('filament/settings.form.missing_item_images.created_at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->actions([
                \Filament\Actions\DeleteAction::make()
                    ->before(function (ItemImage $record) {
                        if ($record->image) {
                            Storage::disk('public')->delete($record->image);
                        }
                    }),
            ])
            ->emptyStateHeading(__('filament/settings.form.missing_item_images.empty'))
            ->emptyStateDescription(__('filament/settings.form.missing_item_images.empty_description'))
            ->emptyStateIcon('heroicon-o-photo');
    }
}
