<?php

namespace App\Filament\Resources\Pages\Pages;

use App\Filament\Resources\Pages\PageResource;
use App\Helpers\SettingHelper;
use App\Models\PageTranslation;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\EditRecord;

class EditPage extends EditRecord
{
    protected static string $resource = PageResource::class;

    public string $activeLocale = 'en';
    public array $translationsData = [];

    protected function afterFill(): void
    {
        $translations = $this->record->translations->keyBy('locale');

        $data = [];
        foreach ($translations as $locale => $t) {
            $data[$locale] = $t->toArray();
        }

        $this->form->fill([
            'slug' => $this->record->slug,
            'published_at' => $this->record->published_at,
            'translations' => $data,
        ]);
    }

    protected function getHeaderActions(): array
    {
        $frontendLanguages = SettingHelper::frontendLanguagesWithLabels();

        if (empty($frontendLanguages) || count($frontendLanguages) <= 1) {
            return [];
        }

        if (!array_key_exists($this->activeLocale, $frontendLanguages)) {
            $this->activeLocale = array_key_first($frontendLanguages) ?? app()->getLocale();
        }

        return [
            Action::make('switchLocale')
                ->label(__('filament/pages.edit.select_language'))
                ->schema([
                    Select::make('locale')
                        ->label(__('filament/pages.edit.select_language_help'))
                        ->options($frontendLanguages)
                        ->default($this->activeLocale)
                        ->native(false)
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $this->activeLocale = $data['locale'];
                    $this->loadTranslation();
                }),

            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }

    public function updatedActiveLocale()
    {
        $this->loadTranslation();
    }

    public function loadTranslation(): void
    {
        $existing = data_get($this->data, "translations.{$this->activeLocale}");

        if ($existing) {
            return;
        }

        $translation = $this->record->translations
            ->firstWhere('locale', $this->activeLocale);

        $current['translations'][$this->activeLocale] = [
            'title' => $translation?->title,
            'content' => $translation?->content,
            'seo_title' => $translation?->seo_title,
            'seo_description' => $translation?->seo_description,
            'is_complete' => $translation?->is_complete ?? false,
        ];

        $current['slug'] = $this->record->slug;
        $current['published_at'] = $this->record->published_at;

        $this->form->fill($current);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->translationsData = $data['translations'] ?? [];
        unset($data['translations']);

        return $data;
    }

    protected function afterSave(): void
    {
        foreach ($this->data['translations'] ?? [] as $locale => $translation) {
            PageTranslation::updateOrCreate(
                [
                    'page_id' => $this->record->id,
                    'locale' => $locale,
                ],
                [
                    'title' => $translation['title'] ?? null,
                    'content' => $translation['content'] ?? null, // 그대로
                    'seo_title' => $translation['seo_title'] ?? null,
                    'seo_description' => $translation['seo_description'] ?? null,
                    'is_complete' => $translation['is_complete'] ?? false,
                ]
            );
        }
    }
}
