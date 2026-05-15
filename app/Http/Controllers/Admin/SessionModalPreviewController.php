<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SessionModal;
use Illuminate\Http\Request;

class SessionModalPreviewController extends Controller
{
    public function show(Request $request, SessionModal $modal)
    {
        return view('admin.session-modal-preview', compact('modal'));
    }
}
