<?php

namespace App\Console\Commands;

use App\Services\TemplateService;
use Illuminate\Console\Command;

class TemplateList extends Command
{
    protected $signature = 'template:list';

    protected $description = 'List all installed templates';

    public function handle(TemplateService $templateService): int
    {
        $templates = $templateService->getTemplates();

        if (empty($templates)) {
            $this->warn('No templates found.');
            return self::SUCCESS;
        }

        $rows = [];
        foreach ($templates as $template) {
            $rows[] = [
                $template['slug'],
                $template['name'],
                $template['metadata']['version'] ?? '-',
                $template['metadata']['author'] ?? '-',
                $template['file_count'],
                $template['is_active'] ? '✓' : '',
            ];
        }

        $this->table(
            ['Slug', 'Name', 'Version', 'Author', 'Files', 'Active'],
            $rows
        );

        return self::SUCCESS;
    }
}
