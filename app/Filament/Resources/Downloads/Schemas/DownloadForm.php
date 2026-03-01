<?php

namespace App\Filament\Resources\Downloads\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DownloadForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('filament/downloads.section.download_details_title'))
                    ->description(__('filament/downloads.section.download_details_description'))
                    ->schema([
                        TextInput::make('name')
                            ->label(__('filament/downloads.form.name'))
                            ->required()
                            ->maxLength(100),

                        TextInput::make('link')
                            ->label(__('filament/downloads.form.link'))
                            ->url()
                            ->required()
                            ->maxLength(512)
                            ->placeholder(__('filament/downloads.form.link_placeholder')),

                        Textarea::make('description')
                            ->label(__('filament/downloads.form.description'))
                            ->rows(3)
                            ->maxLength(255)
                            ->columnSpanFull(),

                        FileUpload::make('image')
                            ->label(__('filament/downloads.form.image'))
                            ->image()
                            ->imageEditor()
                            ->maxSize(5120)
                            ->directory('downloads/images')
                            ->visibility('public')
                            ->columnSpanFull(),
                    ])->columnSpanFull(),
            ]);
    }
}
