<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use ZipArchive;

class TemplateValidator
{
    /** @var string[] */
    protected array $errors = [];

    /**
     * Validate a ZIP file for template installation.
     *
     * @return array{valid: bool, errors: string[], slug: string|null, extract_path: string|null}
     */
    public function validate(string $zipPath): array
    {
        $this->errors = [];

        if (!File::exists($zipPath)) {
            $this->errors[] = 'ZIP file does not exist.';
            return $this->result();
        }

        if (!Str::endsWith(strtolower($zipPath), '.zip')) {
            $this->errors[] = 'File must be a ZIP archive.';
            return $this->result();
        }

        $zip = new ZipArchive();
        $result = $zip->open($zipPath);

        if ($result !== true) {
            $this->errors[] = 'Cannot open ZIP file. It may be corrupted.';
            return $this->result();
        }

        $tempDir = sys_get_temp_dir() . '/template_' . Str::random(16);
        File::makeDirectory($tempDir, 0755, true);

        $zip->extractTo($tempDir);
        $zip->close();

        $templateRoot = $this->findTemplateRoot($tempDir);

        if ($templateRoot === null) {
            File::deleteDirectory($tempDir);
            $this->errors[] = 'Could not determine template root directory.';
            return $this->result();
        }

        $this->validateBladeFiles($templateRoot);
        $this->validateNoExecutablePhp($templateRoot);
        $this->validateFolderStructure($templateRoot);

        // Determine slug from directory name or template.json
        $slug = $this->determineSlug($templateRoot);

        if (!$this->isValidSlug($slug)) {
            $this->errors[] = "Invalid template name '{$slug}'. Use only lowercase letters, numbers, and hyphens.";
        }

        if (!empty($this->errors)) {
            File::deleteDirectory($tempDir);
            return $this->result();
        }

        return $this->result($slug, $templateRoot);
    }

    /**
     * Find the actual template root within extracted ZIP.
     * Handles cases where ZIP contains a single wrapper folder.
     */
    protected function findTemplateRoot(string $extractedPath): ?string
    {
        // Check if blade files exist directly (non-recursive) in this folder
        if ($this->hasDirectBladeFiles($extractedPath) || File::exists($extractedPath . '/template.json')) {
            return $extractedPath;
        }

        // Check for a single subdirectory that contains the template
        $dirs = File::directories($extractedPath);
        if (count($dirs) === 1) {
            $subDir = $dirs[0];
            if ($this->hasBladeFiles($subDir) || File::exists($subDir . '/template.json')) {
                return $subDir;
            }
        }

        // Multiple subdirs: look for one with template.json
        foreach ($dirs as $dir) {
            if (File::exists($dir . '/template.json')) {
                return $dir;
            }
        }

        return null;
    }

    /**
     * Check if a directory has blade files directly (non-recursive).
     */
    protected function hasDirectBladeFiles(string $path): bool
    {
        foreach (File::files($path) as $file) {
            if (Str::endsWith($file->getFilename(), '.blade.php')) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if a directory (recursively) contains blade files.
     */
    protected function hasBladeFiles(string $path): bool
    {
        foreach (File::allFiles($path) as $file) {
            if (Str::endsWith($file->getFilename(), '.blade.php')) {
                return true;
            }
        }
        return false;
    }

    /**
     * Ensure template contains at least one blade file.
     */
    protected function validateBladeFiles(string $templateRoot): void
    {
        if (!$this->hasBladeFiles($templateRoot)) {
            $this->errors[] = 'Template must contain at least one .blade.php file.';
        }
    }

    /**
     * Ensure no executable PHP files exist outside blade templates.
     * Only .blade.php and template.json are allowed.
     */
    protected function validateNoExecutablePhp(string $templateRoot): void
    {
        $allowedExtensions = ['json', 'png', 'jpg', 'jpeg', 'gif', 'svg', 'webp', 'ico', 'css', 'js'];

        foreach (File::allFiles($templateRoot) as $file) {
            $filename = $file->getFilename();
            $extension = strtolower($file->getExtension());

            if (Str::endsWith($filename, '.blade.php')) {
                // Additional security: scan for dangerous PHP constructs in blade files
                $this->validateBladeFileSafety($file->getPathname());
                continue;
            }

            // Allow whitelisted extensions
            if (in_array($extension, $allowedExtensions)) {
                continue;
            }

            // Block plain .php files
            if ($extension === 'php') {
                $this->errors[] = "Executable PHP file detected: {$file->getRelativePathname()}. Only .blade.php files are allowed.";
            }
        }
    }

    /**
     * Scan a blade file for dangerous PHP constructs.
     */
    protected function validateBladeFileSafety(string $filePath): void
    {
        $content = File::get($filePath);
        $filename = basename($filePath);

        $dangerousPatterns = [
            '/\b(eval|exec|system|passthru|shell_exec|popen|proc_open)\s*\(/i',
            '/`[^`]*`/',  // Backtick execution
            '/\bfile_put_contents\s*\(/i',
            '/\bfile_get_contents\s*\(\s*[\'"]https?:\/\//i', // Remote file inclusion
            '/\binclude\s*\(\s*\$/',  // Variable includes
            '/\brequire\s*\(\s*\$/',  // Variable requires
            '/\bbase64_decode\s*\(/i',
            '/\bunserialize\s*\(/i',
        ];

        foreach ($dangerousPatterns as $pattern) {
            if (preg_match($pattern, $content)) {
                $this->errors[] = "Potentially dangerous code detected in {$filename}.";
                break;
            }
        }
    }

    /**
     * Validate folder structure follows Laravel view conventions.
     */
    protected function validateFolderStructure(string $templateRoot): void
    {
        foreach (File::allFiles($templateRoot) as $file) {
            $relativePath = $file->getRelativePathname();

            if (Str::contains($relativePath, ['../', '..\\'])) {
                $this->errors[] = "Path traversal detected in: {$relativePath}";
            }

            $depth = substr_count($relativePath, DIRECTORY_SEPARATOR);
            if ($depth > 5) {
                $this->errors[] = "Template structure too deeply nested: {$relativePath}";
            }
        }
    }

    /**
     * Determine the slug for the template.
     */
    protected function determineSlug(string $templateRoot): string
    {
        $metadataFile = $templateRoot . '/template.json';
        if (File::exists($metadataFile)) {
            $metadata = json_decode(File::get($metadataFile), true);
            if (is_array($metadata) && !empty($metadata['slug'])) {
                return Str::slug($metadata['slug']);
            }
            if (is_array($metadata) && !empty($metadata['name'])) {
                return Str::slug($metadata['name']);
            }
        }

        return Str::slug(basename($templateRoot));
    }

    /**
     * Check if a slug is valid.
     */
    protected function isValidSlug(string $slug): bool
    {
        return (bool) preg_match('/^[a-z0-9]([a-z0-9-]*[a-z0-9])?$/', $slug) && strlen($slug) <= 64;
    }

    /**
     * Build result array.
     *
     * @return array{valid: bool, errors: string[], slug: string|null, extract_path: string|null}
     */
    protected function result(?string $slug = null, ?string $extractPath = null): array
    {
        return [
            'valid' => empty($this->errors),
            'errors' => $this->errors,
            'slug' => $slug,
            'extract_path' => $extractPath,
        ];
    }
}
