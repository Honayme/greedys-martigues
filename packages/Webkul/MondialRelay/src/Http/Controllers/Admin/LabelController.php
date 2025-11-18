<?php

namespace Webkul\MondialRelay\Http\Controllers\Admin;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Webkul\MondialRelay\Services\LabelService;
use Webkul\MondialRelay\Models\OrderMondialRelay;

class LabelController extends Controller
{
    public function __construct(
        protected LabelService $labelService
    ) {}

    /**
     * Génère l'étiquette Mondial Relay
     */
    public function generate(int $orderId): RedirectResponse
    {
        try {
            $result = $this->labelService->generateLabel($orderId);

            session()->flash('success', 'Étiquette générée avec succès. Numéro de suivi: ' . $result['tracking_number']);

        } catch (\Exception $e) {
            session()->flash('error', 'Erreur génération étiquette: ' . $e->getMessage());
        }

        return redirect()->back();
    }

    /**
     * Télécharge/redirige vers l'étiquette PDF
     */
    public function download(int $orderId): RedirectResponse
    {
        try {
            $mrData = OrderMondialRelay::where('order_id', $orderId)->firstOrFail();

            if (empty($mrData->label_url)) {
                session()->flash('error', 'Aucune étiquette générée pour cette commande');
                return redirect()->back();
            }

            // Rediriger vers l'URL de l'étiquette Mondial Relay
            return redirect()->away($mrData->label_url);

        } catch (\Exception $e) {
            session()->flash('error', 'Erreur téléchargement étiquette: ' . $e->getMessage());
            return redirect()->back();
        }
    }
}
