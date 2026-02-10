<?php

namespace App\Http\Controllers;

use App\Models\Template;
use App\Services\TemplateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class TemplateController extends Controller
{
    protected TemplateService $templateService;

    public function __construct(TemplateService $templateService)
    {
        $this->templateService = $templateService;
    }

    /**
     * Display a listing of templates.
     */
    public function index()
    {
        $templates = Template::orderBy('created_at', 'desc')->get();
        $activeTemplate = Template::getActive();
        
        return view('templates.index', compact('templates', 'activeTemplate'));
    }

    /**
     * Show the form for uploading a new template.
     */
    public function create()
    {
        return view('templates.create');
    }

    /**
     * Store a newly uploaded template.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|regex:/^[a-zA-Z0-9_-]+$/',
            'template_file' => 'required|file|mimes:zip|max:51200', // Max 50MB
        ]);

        try {
            $template = $this->templateService->handleUpload(
                $request->input('name'),
                $request->file('template_file')
            );

            return redirect()
                ->route('templates.index')
                ->with('success', 'Template uploaded successfully!');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['template_file' => $e->getMessage()]);
        }
    }

    /**
     * Activate a template.
     */
    public function activate(Template $template)
    {
        $template->activate();

        return redirect()
            ->route('templates.index')
            ->with('success', 'Template activated successfully!');
    }

    /**
     * Deactivate the active template.
     */
    public function deactivate()
    {
        Template::query()->update(['is_active' => false]);

        return redirect()
            ->route('templates.index')
            ->with('success', 'All templates deactivated. Using default theme.');
    }

    /**
     * Remove the specified template.
     */
    public function destroy(Template $template)
    {
        try {
            $this->templateService->deleteTemplate($template);

            return redirect()
                ->route('templates.index')
                ->with('success', 'Template deleted successfully!');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }
}
