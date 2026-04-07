<?php

namespace App\Console\Commands;

use App\Services\TemplateService;
use Illuminate\Console\Command;

class TemplateCacheClear extends Command
{
    protected $signature = 'template:cache-clear';

    protected $description = 'Clear the template resolution cache';

    public function handle(TemplateService $templateService): int
    {
        $templateService->clearCache();

        $this->info('Template cache cleared successfully.');

        return self::SUCCESS;
    }
}
