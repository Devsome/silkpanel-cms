<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SessionModal;
use App\Services\SessionModalService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SessionModalController extends Controller
{
    public function dismiss(Request $request, SessionModalService $service): JsonResponse
    {
        $validated = $request->validate([
            'modal_id' => ['required', 'integer', 'exists:session_modals,id'],
        ]);

        $modal = SessionModal::findOrFail($validated['modal_id']);
        $service->dismiss($modal, $request->user());

        return response()->json(['success' => true]);
    }
}
