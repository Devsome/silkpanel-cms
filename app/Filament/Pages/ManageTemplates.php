<?php

namespace App\Filament\Pages;

use App\Services\TemplateService;
use App\Services\TemplateValidator;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;

class ManageTemplates extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPaintBrush;

    protected string $view = 'filament.pages.manage-templates';

    protected static string|\UnitEnum|null $navigationGroup = 'Configuration';

    protected static ?int $navigationSort = 55;

    /** @var array<string, mixed>|null */
    public ?array $uploadData = [];

    public static function getNavigationLabel(): string
    {
        return __('filament/templates.navigation');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament/settings.navigation_group');
    }

    public function getTitle(): string
    {
        return __('filament/templates.title');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Form::make([
                    FileUpload::make('template_zip')
                        ->label(__('filament/templates.form.template_zip'))
                        ->helperText(__('filament/templates.form.template_zip_helper'))
                        ->acceptedFileTypes(['application/zip', 'application/x-zip-compressed'])
                        ->maxSize(51200) // 50MB
                        ->disk('local')
                        ->directory('temp/templates')
                        ->visibility('private')
                        ->required(),
                ])
                    ->livewireSubmitHandler('uploadTemplate')
                    ->footer([
                        \Filament\Schemas\Components\Actions::make([
                            Action::make('upload')
                                ->label(__('filament/templates.form.upload_button'))
                                ->icon('heroicon-o-arrow-up-tray')
                                ->submit('uploadTemplate'),
                        ]),
                    ]),
            ])
            ->statePath('uploadData');
    }

    /**
     * Get all templates for display.
     *
     * @return array<string, array{name: string, slug: string, path: string, is_active: bool, metadata: array|null, file_count: int}>
     */
    #[Computed]
    public function templates(): array
    {
        return app(TemplateService::class)->getTemplates();
    }

    /**
     * Handle template ZIP upload and installation.
     */
    public function uploadTemplate(): void
    {
        $data = $this->form->getState();

        if (empty($data['template_zip'])) {
            Notification::make()
                ->danger()
                ->title(__('filament/templates.notifications.title_error'))
                ->body(__('filament/templates.notifications.please_select_file'))
                ->send();
            return;
        }

        $zipPath = Storage::disk('local')->path($data['template_zip']);

        if (!File::exists($zipPath)) {
            Notification::make()
                ->danger()
                ->title(__('filament/templates.notifications.title_error'))
                ->body(__('filament/templates.notifications.upload_error'))
                ->send();
            return;
        }

        $validator = new TemplateValidator();
        $result = $validator->validate($zipPath);

        Storage::disk('local')->delete($data['template_zip']);

        if (!$result['valid']) {
            Notification::make()
                ->danger()
                ->title(__('filament/templates.notifications.title_error'))
                ->body(implode("\n", $result['errors']))
                ->persistent()
                ->send();

            if ($result['extract_path'] && File::isDirectory($result['extract_path'])) {
                File::deleteDirectory($result['extract_path']);
            }
            return;
        }

        try {
            $templateService = app(TemplateService::class);

            if ($templateService->templateExists($result['slug'])) {
                // Clean up the existing one and reinstall
                $templateService->deleteTemplate($result['slug']);
            }

            $templateService->installTemplate($result['extract_path'], $result['slug']);

            // Clean up temp extraction
            File::deleteDirectory($result['extract_path']);

            Notification::make()
                ->success()
                ->title(__('filament/templates.notifications.title_success'))
                ->body(__('filament/templates.notifications.upload_success'))
                ->send();

            $this->uploadData = [];
            unset($this->templates);
        } catch (\Throwable $e) {
            if ($result['extract_path'] && File::isDirectory($result['extract_path'])) {
                File::deleteDirectory($result['extract_path']);
            }

            Notification::make()
                ->danger()
                ->title(__('filament/templates.notifications.title_error'))
                ->body($e->getMessage())
                ->send();
        }
    }

    /**
     * Activate a template.
     */
    public function activateTemplate(string $slug): void
    {
        try {
            app(TemplateService::class)->setActiveTemplate($slug);

            Notification::make()
                ->success()
                ->title(__('filament/templates.notifications.title_success'))
                ->body(__('filament/templates.notifications.activate_success', ['slug' => $slug]))
                ->send();

            unset($this->templates);
        } catch (\Throwable $e) {
            Notification::make()
                ->danger()
                ->title(__('filament/templates.notifications.title_error'))
                ->body($e->getMessage())
                ->send();
        }
    }

    /**
     * Deactivate the current template.
     */
    public function deactivateTemplate(): void
    {
        try {
            app(TemplateService::class)->deactivateTemplate();

            Notification::make()
                ->success()
                ->title(__('filament/templates.notifications.title_success'))
                ->body(__('filament/templates.notifications.deactivate_success'))
                ->send();

            unset($this->templates);
        } catch (\Throwable $e) {
            Notification::make()
                ->danger()
                ->title(__('filament/templates.notifications.title_error'))
                ->body($e->getMessage())
                ->send();
        }
    }

    /**
     * Delete a template.
     */
    public function deleteTemplate(string $slug): void
    {
        try {
            app(TemplateService::class)->deleteTemplate($slug);

            Notification::make()
                ->success()
                ->title(__('filament/templates.notifications.title_success'))
                ->body(__('filament/templates.notifications.delete_success', ['slug' => $slug]))
                ->send();

            unset($this->templates);
        } catch (\Throwable $e) {
            Notification::make()
                ->danger()
                ->title(__('filament/templates.notifications.title_error'))
                ->body($e->getMessage())
                ->send();
        }
    }

    /**
     * Download the starter template skeleton as a ZIP.
     */
    public function downloadStarter(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $starterPath = resource_path('views');
        $zipPath = sys_get_temp_dir() . '/starter-template-' . now()->timestamp . '.zip';

        $zip = new \ZipArchive();
        $zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

        $zip->addFromString('my-template/template.json', json_encode([
            'name' => 'My Custom Template',
            'slug' => 'my-template',
            'version' => '1.0.0',
            'author' => 'Your Name',
            'description' => 'A custom template based on the starter skeleton.',
            'preview_image' => 'preview.png',
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        // Provide a starter assets/app.css that hooks into the compiled main CSS.
        // @reference lets you use @apply with the main theme's colors/fonts without
        // duplicating the entire Tailwind output. Add your own custom CSS rules here.
        $starterAssetsCss = <<<'CSS'
/*
 * Template-specific CSS entry point.
 *
 * @reference points to the main app.css so you can use @apply with all
 * custom colors/fonts defined there — without duplicating the full Tailwind output.
 *
 * Example:
 *   .my-hero { @apply text-4xl font-bold text-primary-500; }
 *
 * Vite compiles this file automatically when it is present on disk.
 * @templateStyles in your layout loads it automatically — no @vite() call needed.
 */
@reference "../../../../css/app.css";

/* Add your custom template styles below */

.custom-hero-header {
    @apply text-4xl font-bold text-primary-500;
    color: red;
}
CSS;

        $zip->addFromString('my-template/assets/app.css', $starterAssetsCss);

        $skeletonFiles = [
            'welcome.blade.php',
            'layouts/app.blade.php',
            'auth/login.blade.php',
            'auth/register.blade.php',
            'auth/forgot-password.blade.php',
            'dashboard.blade.php',
            'payment.blade.php',
        ];

        foreach ($skeletonFiles as $file) {
            $fullPath = $starterPath . '/' . $file;
            if (File::exists($fullPath)) {
                $zip->addFile($fullPath, 'my-template/' . $file);
            }
        }

        $zip->close();

        return response()->streamDownload(function () use ($zipPath) {
            readfile($zipPath);
            File::delete($zipPath);
        }, 'starter-template.zip', [
            'Content-Type' => 'application/zip',
        ]);
    }
}
