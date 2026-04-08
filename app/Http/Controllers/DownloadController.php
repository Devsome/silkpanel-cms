<?php

namespace App\Http\Controllers;

use App\Models\Download;
use Illuminate\View\View;

class DownloadController extends Controller
{
    public function index(): View
    {
        $downloads = Download::orderBy('name')->get();

        return view('template::downloads.index', compact('downloads'));
    }
}
