<?php

namespace Webkul\MondialRelay\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Webkul\MondialRelay\Services\MondialRelayApi;

class PointRelaisController extends Controller
{
    public function __construct(
        protected MondialRelayApi $api
    ) {}

    /**
     * Recherche des points relais proches
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'postcode' => 'required|string|min:4|max:10',
            'country' => 'sometimes|string|size:2',
        ]);

        try {
            $points = $this->api->searchPointRelais(
                $request->input('postcode'),
                $request->input('country', 'FR'),
                10
            );

            return response()->json([
                'success' => true,
                'points' => $points,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
