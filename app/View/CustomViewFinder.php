<?php

namespace App\View;

use App\Services\TemplateService;
use Illuminate\View\FileViewFinder as BaseFileViewFinder;

class CustomViewFinder extends BaseFileViewFinder
{
    protected TemplateService $templateService;

    /**
     * Set the template service instance.
     */
    public function setTemplateService(TemplateService $templateService): void
    {
        $this->templateService = $templateService;
    }

    /**
     * Find the given view in the list of paths.
     *
     * @param  string  $name
     * @param  array  $paths
     * @return string
     */
    protected function findInPaths($name, $paths)
    {
        // First, check if there's a custom template view
        if (isset($this->templateService)) {
            $customView = $this->templateService->findView($name);
            if ($customView !== null && file_exists($customView)) {
                return $customView;
            }
        }

        // Fall back to default behavior
        return parent::findInPaths($name, $paths);
    }
}
