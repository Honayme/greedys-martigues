<?php

namespace Webkul\MondialRelay\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SaveMondialRelayData
{
    /**
     * Intercepte la sauvegarde du shipping method pour capturer les données MR
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Si c'est la route de sauvegarde du shipping method
        if ($request->route()->getName() === 'shop.checkout.onepage.shipping_methods.store') {
            $shippingMethod = $request->input('shipping_method');

            // Si c'est Mondial Relay Point Relais ou Locker
            if (str_starts_with($shippingMethod, 'mondialrelay_') &&
                (str_contains($shippingMethod, 'point_relais') || str_contains($shippingMethod, 'locker'))) {

                // Récupérer les données du point relais depuis le request
                $pointData = $request->input('mondial_relay_point');

                if ($pointData) {
                    session(['mondial_relay_selected_point' => $pointData]);
                }
            }
        }

        return $response;
    }
}
