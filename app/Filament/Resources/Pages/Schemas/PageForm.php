<?php

namespace App\Filament\Resources\Pages\Schemas;

use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class PageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('filament/pages.edit.sections.translations'))
                    ->schema(
                        fn(callable $get, callable $set, $livewire) =>
                        static::getTranslationSchema($livewire)
                    )
                    ->columns(3)
                    ->columnSpan(3),
                Section::make(__('filament/pages.edit.sections.page'))
                    ->schema([
                        TextInput::make('slug')
                            ->label(__('filament/pages.edit.slug'))
                            ->helperText(__('filament/pages.edit.slug_help'))
                            ->required()
                            ->unique(ignoreRecord: true),

                        DateTimePicker::make('published_at')
                            ->label(__('filament/pages.edit.published_at')),
                    ])
                    ->columnSpan(1),
            ])
            ->columns(4);
    }

    public static function getTranslationSchema($livewire): array
    {
        $locale = $livewire->activeLocale ?? 'en';

        return [
            TextInput::make("translations.$locale.title")
                ->label(__('filament/pages.edit.page_title'))
                ->required()
                ->columnSpan(1),

            TextInput::make("translations.$locale.seo_title")
                ->label(__('filament/pages.edit.seo_title'))
                ->reactive()
                ->helperText(function ($state) {
                    $length = mb_strlen($state ?? '');
                    return __('filament/pages.edit.seo_title_max_length', ['length' => $length]);
                })
                ->maxLength(60)
                ->columnSpan(1),

            Toggle::make("translations.$locale.is_complete")
                ->label(__('filament/pages.edit.is_complete'))
                ->helperText(__('filament/pages.edit.is_complete_help'))
                ->inline(false)
                ->offIcon(Heroicon::XMark)
                ->default(false)
                ->columnSpan(1),

            Textarea::make("translations.$locale.seo_description")
                ->label(__('filament/pages.edit.seo_description'))
                ->reactive()
                ->helperText(function ($state) {
                    $length = mb_strlen($state ?? '');
                    return __('filament/pages.edit.seo_description_max_length', ['length' => $length]);
                })
                ->maxLength(160)
                ->columnSpan(3),

            Section::make(__('filament/pages.edit.sections.content_blocks'))
                ->description(__('filament/pages.edit.sections.content_blocks_help'))
                ->secondary(true)
                ->schema([
                    Builder::make("translations.$locale.content")
                        ->blocks([
                            Block::make('heading')
                                ->schema([
                                    TextInput::make('headline')
                                        ->label(__('filament/pages.edit.builder.headline'))
                                        ->required(),
                                    TextInput::make('subheadline')
                                        ->label(__('filament/pages.edit.builder.subheadline')),
                                    Select::make('level')
                                        ->label(__('filament/pages.edit.builder.headline_level'))
                                        ->options([
                                            'h1' => __('filament/pages.edit.builder.heading_one'),
                                            'h2' => __('filament/pages.edit.builder.heading_two'),
                                            'h3' => __('filament/pages.edit.builder.heading_three'),
                                        ])
                                        ->required(),
                                ])
                                ->columns(3),
                            Block::make('paragraph')
                                ->schema([
                                    Textarea::make('paragraph')
                                        ->label(__('filament/pages.edit.builder.paragraph'))
                                        ->required(),
                                ]),
                            Block::make('rich_text')
                                ->schema([
                                    RichEditor::make('rich_text')
                                        ->label(__('filament/pages.edit.builder.rich_text'))
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
                                ]),
                            Block::make('image')
                                ->schema([
                                    FileUpload::make('url')
                                        ->label(__('filament/pages.edit.builder.image'))
                                        ->image()
                                        ->required(),
                                    TextInput::make('alt')
                                        ->label(__('filament/pages.edit.builder.alt_text'))
                                        ->required(),
                                ]),
                        ]),
                ])->columnSpanFull(),
        ];
    }
}
