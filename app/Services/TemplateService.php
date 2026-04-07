<?php

namespace App\Services;

use App\Helpers\SettingHelper;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class TemplateService
{
    protected string $templatesPath;

    protected const CACHE_KEY_ACTIVE = 'template.active';
    protected const CACHE_KEY_RESOLVED_PREFIX = 'template.resolved.';
    protected const CACHE_TTL = 3600; // 1 hour

    public function __construct()
    {
        $this->templatesPath = resource_path('views/templates');
    }

    /**
     * Get the currently active template name (null = no template active).
     */
    public function getActiveTemplate(): ?string
    {
        return Cache::remember(self::CACHE_KEY_ACTIVE, self::CACHE_TTL, function () {
            $template = SettingHelper::get('active_template', '');

            return $template !== '' ? $template : null;
        });
    }

    /**
     * Set the active template and clear cache.
     */
    public function setActiveTemplate(string $template): void
    {
        if (!$this->templateExists($template)) {
            throw new \InvalidArgumentException("Template '{$template}' does not exist.");
        }

        SettingHelper::set('active_template', $template);
        $this->clearCache();
    }

    /**
     * Deactivate the current template (fall back to root views).
     */
    public function deactivateTemplate(): void
    {
        SettingHelper::set('active_template', '');
        $this->clearCache();
    }

    /**
     * Resolve a view name to the correct template path.
     * Checks active template first, falls back to root views.
     */
    public function resolveViewPath(string $view): string
    {
        $activeTemplate = $this->getActiveTemplate();

        if ($activeTemplate === null) {
            return $view;
        }

        $cacheKey = self::CACHE_KEY_RESOLVED_PREFIX . $activeTemplate . '.' . $view;

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($activeTemplate, $view) {
            $activeViewPath = $this->viewFilePath($activeTemplate, $view);

            if (File::exists($activeViewPath)) {
                return "templates.{$activeTemplate}.{$view}";
            }

            return $view;
        });
    }

    /**
     * Check if a template directory exists.
     */
    public function templateExists(string $template): bool
    {
        return File::isDirectory($this->templatePath($template));
    }

    /**
     * Get all available templates with their metadata.
     *
     * @return array<string, array{name: string, path: string, is_active: bool, metadata: array|null}>
     */
    public function getTemplates(): array
    {
        $templates = [];
        $activeTemplate = $this->getActiveTemplate();

        if (!File::isDirectory($this->templatesPath)) {
            return $templates;
        }

        foreach (File::directories($this->templatesPath) as $dir) {
            $name = basename($dir);
            $metadata = $this->getTemplateMetadata($name);

            $templates[$name] = [
                'name' => $metadata['name'] ?? Str::title(str_replace('-', ' ', $name)),
                'slug' => $name,
                'path' => $dir,
                'is_active' => $name === $activeTemplate,
                'metadata' => $metadata,
                'file_count' => $this->countBladeFiles($dir),
            ];
        }

        return $templates;
    }

    /**
     * Get template metadata from template.json.
     */
    public function getTemplateMetadata(string $template): ?array
    {
        $metadataFile = $this->templatePath($template) . '/template.json';

        if (!File::exists($metadataFile)) {
            return null;
        }

        $content = File::get($metadataFile);
        $decoded = json_decode($content, true);

        return is_array($decoded) ? $decoded : null;
    }

    /**
     * Install a template from an extracted directory.
     */
    public function installTemplate(string $sourcePath, string $templateSlug): void
    {
        $targetPath = $this->templatePath($templateSlug);

        if (File::isDirectory($targetPath)) {
            throw new \RuntimeException("Template '{$templateSlug}' already exists.");
        }

        File::copyDirectory($sourcePath, $targetPath);
    }

    /**
     * Delete a template.
     */
    public function deleteTemplate(string $template): void
    {
        if ($this->getActiveTemplate() === $template) {
            $this->deactivateTemplate();
        }

        $path = $this->templatePath($template);

        if (File::isDirectory($path)) {
            File::deleteDirectory($path);
        }

        $this->clearCache();
    }

    /**
     * Get the list of view files a template provides (relative paths).
     *
     * @return string[]
     */
    public function getTemplateViews(string $template): array
    {
        $path = $this->templatePath($template);

        if (!File::isDirectory($path)) {
            return [];
        }

        $views = [];
        foreach (File::allFiles($path) as $file) {
            if ($file->getExtension() === 'php' && Str::endsWith($file->getFilename(), '.blade.php')) {
                $views[] = $file->getRelativePathname();
            }
        }

        return $views;
    }

    /**
     * Get absolute path for a template directory.
     */
    public function templatePath(string $template): string
    {
        return $this->templatesPath . '/' . $template;
    }

    /**
     * Get absolute file path for a view within a template.
     */
    protected function viewFilePath(string $template, string $view): string
    {
        $relativePath = str_replace('.', '/', $view) . '.blade.php';
        return $this->templatePath($template) . '/' . $relativePath;
    }

    /**
     * Count blade files in a template directory.
     */
    protected function countBladeFiles(string $path): int
    {
        $count = 0;
        foreach (File::allFiles($path) as $file) {
            if (Str::endsWith($file->getFilename(), '.blade.php')) {
                $count++;
            }
        }
        return $count;
    }

    /**
     * Get the absolute path to a template's preview image.
     */
    public function getPreviewImagePath(string $template): ?string
    {
        $metadata = $this->getTemplateMetadata($template);

        if (!$metadata || empty($metadata['preview_image'])) {
            return null;
        }

        $filename = basename($metadata['preview_image']);
        $path = $this->templatePath($template) . '/' . $filename;

        if (!File::exists($path)) {
            return null;
        }

        return $path;
    }

    /**
     * Clear all template-related caches.
     */
    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY_ACTIVE);

        foreach ($this->getTemplatesWithoutCache() as $template) {
            $prefix = self::CACHE_KEY_RESOLVED_PREFIX . $template . '.';
            // Since we can't enumerate all possible views, we use a tag approach
            // or simply forget the active template cache
            Cache::forget($prefix);
        }
    }

    /**
     * Get template slugs without using cache.
     *
     * @return string[]
     */
    protected function getTemplatesWithoutCache(): array
    {
        if (!File::isDirectory($this->templatesPath)) {
            return [];
        }

        return array_map('basename', File::directories($this->templatesPath));
    }
}
