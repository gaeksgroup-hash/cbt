<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HealthController extends Controller
{
    /**
     * Handle the health probe check.
     */
    public function __invoke(Request $request): JsonResponse
    {
        return response()->json([
            'status' => 'ok',
            'service' => 'gaeks-cbt',
            'timestamp' => now()->toIso8601String(),
        ], 200);
    }
}
