<?php

namespace App\Filament\Resources\Pages\Pages;

use App\Filament\Resources\Pages\PageResource;
use App\Models\PageTranslation;
use Filament\Resources\Pages\CreateRecord;

class CreatePage extends CreateRecord
{
    protected static string $resource = PageResource::class;

    public string $activeLocale = 'en';
    public array $translationsData = [];

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->translationsData = $data['translations'] ?? [];

        unset($data['translations']);

        return $data;
    }

    protected function afterCreate(): void
    {
        $this->saveTranslations();
    }

    protected function saveTranslations(): void
    {
        foreach ($this->translationsData as $locale => $translation) {
            PageTranslation::updateOrCreate(
                [
                    'page_id' => $this->record->id,
                    'locale' => $locale,
                ],
                [
                    'title' => $translation['title'] ?? null,
                    'content' => $translation['content'] ?? [],
                    'seo_title' => $translation['seo_title'] ?? null,
                    'seo_description' => $translation['seo_description'] ?? null,
                    'is_complete' => $translation['is_complete'] ?? false,
                ]
            );
        }
    }
}
