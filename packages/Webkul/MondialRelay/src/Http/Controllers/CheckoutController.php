<?php

namespace Webkul\MondialRelay\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class CheckoutController extends Controller
{
    /**
     * Sauvegarde les données du point relais sélectionné
     */
    public function savePointRelais(Request $request): JsonResponse
    {
        $pointData = $request->input('point');

        if ($pointData) {
            session(['mondial_relay_selected_point' => $pointData]);

            return response()->json([
                'success' => true,
                'message' => 'Point relais sauvegardé',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Données invalides',
        ], 400);
    }
}
