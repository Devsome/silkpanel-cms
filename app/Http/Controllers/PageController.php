<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\View\View;

class PageController extends Controller
{
    public function show(string $slug): View
    {
        $page = Page::where('slug', $slug)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->firstOrFail();

        $translation = $page->translation(app()->getLocale())->first();

        if (!$translation) {
            $translation = $page->translation(config('app.fallback_locale'))->first();
        }

        abort_unless($translation, 404);

        return view('template::pages.show', compact('page', 'translation'));
    }
}
