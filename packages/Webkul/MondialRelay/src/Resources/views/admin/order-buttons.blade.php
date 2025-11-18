@php
    $order = $order ?? null;
    if (!$order || !str_starts_with($order->shipping_method ?? '', 'mondialrelay_')) {
        return;
    }

    $mrData = \Webkul\MondialRelay\Models\OrderMondialRelay::where('order_id', $order->id)->first();
@endphp

@if($mrData)
    <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
        <h3 class="text-lg font-semibold mb-3">Mondial Relay</h3>

        <div class="grid grid-cols-2 gap-2 text-sm mb-4">
            <div>
                <span class="font-medium">Mode:</span>
                @switch($mrData->delivery_mode)
                    @case('24R')
                        Point Relais
                        @break
                    @case('24L')
                        Locker
                        @break
                    @case('LD1')
                        Domicile
                        @break
                @endswitch
            </div>

            @if($mrData->point_relais_id)
                <div>
                    <span class="font-medium">Point Relais:</span> {{ $mrData->point_relais_name }}
                </div>
                <div class="col-span-2 text-xs text-gray-600">
                    {{ $mrData->point_relais_address }}, {{ $mrData->point_relais_postcode }} {{ $mrData->point_relais_city }}
                </div>
            @endif

            @if($mrData->tracking_number)
                <div class="col-span-2">
                    <span class="font-medium">Numéro de suivi:</span>
                    <code class="bg-white px-2 py-1 rounded">{{ $mrData->tracking_number }}</code>
                </div>
            @endif
        </div>

        <div class="flex gap-2">
            @if(!$mrData->label_url)
                <form action="{{ route('admin.mondialrelay.generate_label', $order->id) }}" method="POST">
                    @csrf
                    <button
                        type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm"
                    >
                        Générer l'étiquette
                    </button>
                </form>
            @else
                <a
                    href="{{ route('admin.mondialrelay.download_label', $order->id) }}"
                    target="_blank"
                    class="inline-block px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm"
                >
                    Télécharger l'étiquette
                </a>

                <form action="{{ route('admin.mondialrelay.generate_label', $order->id) }}" method="POST">
                    @csrf
                    <button
                        type="submit"
                        class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 text-sm"
                    >
                        Régénérer l'étiquette
                    </button>
                </form>
            @endif
        </div>
    </div>
@endif
