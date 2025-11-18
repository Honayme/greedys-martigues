@php
    $mrData = \Webkul\MondialRelay\Models\OrderMondialRelay::where('order_id', $order->id)->first();
@endphp

@if($mrData && str_starts_with($order->shipping_method, 'mondialrelay'))
    <div class="box-shadow mt-8 rounded bg-white p-4 dark:bg-gray-900">
        <!-- Header -->
        <div class="mb-4 flex items-center justify-between border-b pb-4">
            <p class="text-base font-semibold text-gray-800 dark:text-white">
                📦 Informations Mondial Relay
            </p>
        </div>

        <!-- Mode de livraison -->
        <div class="mb-4">
            <p class="text-sm font-semibold text-gray-600 dark:text-gray-300">
                Mode de livraison
            </p>
            <p class="mt-1 text-gray-800 dark:text-white">
                @if($mrData->delivery_mode === '24R')
                    Point Relais (24R)
                @elseif($mrData->delivery_mode === '24L')
                    Consigne automatique - Locker (24L)
                @elseif($mrData->delivery_mode === 'LD1')
                    Livraison à domicile (LD1)
                @else
                    {{ $mrData->delivery_mode }}
                @endif
            </p>
        </div>

        <!-- Point relais (si applicable) -->
        @if($mrData->delivery_mode !== 'LD1' && $mrData->point_relais_id)
            <div class="mb-4 rounded bg-gray-50 p-3 dark:bg-gray-800">
                <p class="mb-2 text-sm font-semibold text-gray-600 dark:text-gray-300">
                    Point relais sélectionné
                </p>
                <div class="space-y-1 text-sm text-gray-700 dark:text-gray-200">
                    <p class="font-medium">{{ $mrData->point_relais_name }}</p>
                    <p>{{ $mrData->point_relais_address }}</p>
                    <p>{{ $mrData->point_relais_postcode }} {{ $mrData->point_relais_city }}</p>
                    <p class="text-xs text-gray-500">ID: {{ $mrData->point_relais_id }}</p>
                </div>
            </div>
        @endif

        <!-- Statut et actions -->
        <div class="border-t pt-4">
            @if(empty($mrData->tracking_number))
                <!-- Pas encore d'étiquette -->
                <div class="mb-3 rounded bg-yellow-50 p-3 text-sm text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-200">
                    ⚠️ Étiquette non générée
                </div>

                <form method="POST" action="{{ route('admin.mondialrelay.generate_label', $order->id) }}">
                    @csrf
                    <button
                        type="submit"
                        class="inline-flex items-center rounded border border-blue-600 bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-700"
                    >
                        <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Générer l'étiquette Mondial Relay
                    </button>
                </form>
            @else
                <!-- Étiquette générée -->
                <div class="mb-3 rounded bg-green-50 p-3 dark:bg-green-900/20">
                    <p class="text-sm font-semibold text-green-800 dark:text-green-200">
                        ✓ Étiquette générée
                    </p>
                    <p class="mt-1 text-sm text-green-700 dark:text-green-300">
                        Numéro de suivi : <span class="font-mono font-semibold">{{ $mrData->tracking_number }}</span>
                    </p>
                </div>

                <div class="flex gap-3">
                    <!-- Télécharger l'étiquette -->
                    <a
                        href="{{ route('admin.mondialrelay.download_label', $order->id) }}"
                        target="_blank"
                        class="inline-flex items-center rounded border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700"
                    >
                        <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Télécharger l'étiquette PDF
                    </a>

                    <!-- Suivre le colis -->
                    <a
                        href="https://www.mondialrelay.fr/suivi-de-colis/?numeroExpedition={{ $mrData->tracking_number }}"
                        target="_blank"
                        class="inline-flex items-center rounded border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700"
                    >
                        <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Suivre le colis
                    </a>
                </div>
            @endif
        </div>
    </div>
@endif
