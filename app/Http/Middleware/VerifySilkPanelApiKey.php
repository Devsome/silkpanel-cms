<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifySilkPanelApiKey
{
    public function handle(Request $request, Closure $next): Response
    {
        $expected = config('silkpanel.api_key');
        $provided = $request->header('X-SILKPANEL');

        if (empty($expected) || $provided !== $expected) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 401);
        }

        return $next($request);
    }
}
