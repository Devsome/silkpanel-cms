<?php

namespace App\Services;

use App\Models\Template;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class TemplateService
{
    /**
     * Get the path to custom templates.
     */
    public function getTemplatesPath(): string
    {
        return storage_path('app/templates');
    }

    /**
     * Get the active template path if exists.
     */
    public function getActiveTemplatePath(): ?string
    {
        $activeTemplate = Template::getActive();
        
        if (!$activeTemplate) {
            return null;
        }

        $path = $this->getTemplatesPath() . '/' . $activeTemplate->path;
        
        return File::isDirectory($path) ? $path : null;
    }

    /**
     * Handle ZIP file upload and extraction.
     */
    public function handleUpload(string $name, $file): Template
    {
        $templatePath = $this->getTemplatesPath() . '/' . $name;
        
        // Create template directory if it doesn't exist
        if (!File::isDirectory($templatePath)) {
            File::makeDirectory($templatePath, 0755, true);
        }

        if ($file->getClientOriginalExtension() === 'zip') {
            // Handle ZIP file
            $zipPath = $file->storeAs('temp', $name . '.zip');
            $fullZipPath = Storage::path($zipPath);
            
            $zip = new ZipArchive;
            if ($zip->open($fullZipPath) === true) {
                // Extract directly to template directory
                $zip->extractTo($templatePath);
                $zip->close();
                
                // Clean up ZIP file
                Storage::delete($zipPath);
            } else {
                throw new \Exception('Failed to extract ZIP file');
            }
        } else {
            throw new \Exception('Only ZIP files are supported');
        }

        // Validate the extracted template
        $this->validateTemplate($templatePath);

        // Create or update template record
        $template = Template::updateOrCreate(
            ['name' => $name],
            ['path' => $name]
        );

        return $template;
    }

    /**
     * Validate that the template has valid structure.
     */
    protected function validateTemplate(string $path): void
    {
        // Check if the path exists
        if (!File::isDirectory($path)) {
            throw new \Exception('Template directory does not exist');
        }

        // Ensure only safe files (no PHP executables except .blade.php)
        $files = File::allFiles($path);
        foreach ($files as $file) {
            $extension = $file->getExtension();
            $basename = $file->getBasename();
            
            // Allow .blade.php, .html, .css, .js, .json files
            $allowedExtensions = ['php', 'html', 'css', 'js', 'json', 'svg', 'png', 'jpg', 'jpeg', 'gif', 'webp'];
            
            if (!in_array($extension, $allowedExtensions)) {
                throw new \Exception("File type not allowed: {$basename}");
            }
            
            // Ensure .php files are blade templates
            if ($extension === 'php' && !str_ends_with($basename, '.blade.php')) {
                throw new \Exception("Only Blade templates (.blade.php) are allowed: {$basename}");
            }
        }
    }

    /**
     * Delete a template and its files.
     */
    public function deleteTemplate(Template $template): void
    {
        $templatePath = $this->getTemplatesPath() . '/' . $template->path;
        
        if (File::isDirectory($templatePath)) {
            File::deleteDirectory($templatePath);
        }
        
        $template->delete();
    }

    /**
     * Find a view in the active template or return null.
     */
    public function findView(string $name): ?string
    {
        $activeTemplatePath = $this->getActiveTemplatePath();
        
        if (!$activeTemplatePath) {
            return null;
        }

        // Convert dot notation to path
        $viewPath = str_replace('.', '/', $name);
        $possiblePath = $activeTemplatePath . '/views/' . $viewPath . '.blade.php';
        
        if (File::exists($possiblePath)) {
            return $possiblePath;
        }

        return null;
    }
}
