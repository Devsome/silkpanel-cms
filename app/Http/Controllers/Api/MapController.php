<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\SilkroadMapService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MapController extends Controller
{
    public function __construct(private readonly SilkroadMapService $mapService) {}

    /**
     * Return all online character positions as JSON.
     *
     * GET /api/map/characters
     * Requires authentication. Job-suit characters are excluded from the response.
     */
    public function characters(): JsonResponse
    {
        return $this->respond(adminView: false);
    }

    /**
     * Admin variant — includes job-suit characters with the has_job_suit flag.
     * Pass ?include_offline=1 to also include offline characters (marked with is_online=false).
     *
     * GET /api/admin/map/characters
     * Requires admin/supporter role (enforced by FilamentAdminMiddleware on the route).
     */
    public function adminCharacters(Request $request): JsonResponse
    {
        return $this->respond(adminView: true, includeOffline: $request->boolean('include_offline'));
    }

    private function respond(bool $adminView, bool $includeOffline = false): JsonResponse
    {
        try {
            $characters = $includeOffline
                ? $this->mapService->getAllCharacterPositions()
                : $this->mapService->getOnlineCharacterPositions(adminView: $adminView);

            return response()->json([
                'data'  => $characters->values(),
                'total' => $characters->count(),
                'limit' => (int) Setting::get('map_max_characters', SilkroadMapService::MAX_CHARACTERS),
            ]);
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'data'  => [],
                'total' => 0,
                'limit' => (int) Setting::get('map_max_characters', SilkroadMapService::MAX_CHARACTERS),
                'error' => 'Unable to load character positions.',
            ], 503);
        }
    }
}
