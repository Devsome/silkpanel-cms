<?php

namespace App\Filament\Clusters\Settings\Pages;

use App\Filament\Clusters\Settings;
use App\Models\ItemImage;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
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
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

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
                    TextInput::make('codename')
                        ->label(__('filament/settings.form.missing_item_images.codename'))
                        ->helperText(__('filament/settings.form.missing_item_images.codename_description'))
                        ->placeholder('item/china/weapon/bow_08')
                        ->required()
                        ->maxLength(255)
                        ->unique(ItemImage::class, 'codename'),

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

        $codename = strtolower(trim($data['codename'], " /\\"));
        $codename = str_replace('\\', '/', $codename);
        $codename = preg_replace('/\.png$/i', '', $codename);

        if (str_contains($codename, '/')) {
            $targetPath = public_path('images/silkroad/' . $codename . '.png');
            $targetDir = dirname($targetPath);

            if (!File::isDirectory($targetDir)) {
                File::makeDirectory($targetDir, 0755, true);
            }

            $storagePath = Storage::disk('public')->path($data['image']);
            File::copy($storagePath, $targetPath);
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
                        if (str_contains($record->codename, '/')) {
                            $publicPath = public_path('images/silkroad/' . $record->codename . '.png');
                            if (File::exists($publicPath)) {
                                File::delete($publicPath);
                            }
                        }

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
