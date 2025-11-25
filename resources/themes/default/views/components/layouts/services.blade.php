{!! view_render_event('bagisto.shop.layout.features.before') !!}

<!-- Trust Bar - Mondial Relay / Stripe / Acier Inoxydable / Click & Collect -->
<div class="w-full bg-white py-10 mb-10 pt-7">
    <div class="container mx-auto px-4">

        <div class="flex flex-wrap min-[1100px]:flex-nowrap items-center justify-between gap-8">

            <div class="flex items-center gap-4 w-[calc(50%-1rem)] min-[1100px]:w-auto min-[1100px]:flex-1">
                <div class="flex h-16 w-16 flex-shrink-0 items-center justify-center rounded-full border border-gray-900 bg-white text-gray-900">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="1" y="3" width="15" height="13"></rect>
                        <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon>
                        <circle cx="5.5" cy="18.5" r="2.5"></circle>
                        <circle cx="18.5" cy="18.5" r="2.5"></circle>
                    </svg>
                </div>
                <div class="flex flex-col">
                    <h3 class=" text-md font-bold text-gray-900 leading-tight">
                        Envoi Mondial Relay
                    </h3>
                    <span class="font-sans text-sm text-gray-500 mt-1">
                        Livraison rapide & suivie
                    </span>
                </div>
            </div>

            <div class="flex items-center gap-4 w-[calc(50%-1rem)] min-[1100px]:w-auto min-[1100px]:flex-1">
                <div class="flex h-16 w-16 flex-shrink-0 items-center justify-center rounded-full border border-gray-900 bg-white text-gray-900">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="5" width="20" height="14" rx="2"></rect>
                        <line x1="2" y1="10" x2="22" y2="10"></line>
                    </svg>
                </div>
                <div class="flex flex-col">
                    <h3 class=" text-md font-bold text-gray-900 leading-tight">
                        Paiement Sécurisé
                    </h3>
                    <span class="font-sans text-sm text-gray-500 mt-1">
                        Via Stripe & CB
                    </span>
                </div>
            </div>

            <div class="flex items-center gap-4 w-[calc(50%-1rem)] min-[1100px]:w-auto min-[1100px]:flex-1">
                <div class="flex h-16 w-16 flex-shrink-0 items-center justify-center rounded-full border border-gray-900 bg-white text-gray-900">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M6 3h12l4 6-10 13L2 9z"></path>
                    </svg>
                </div>
                <div class="flex flex-col">
                    <h3 class=" text-md font-bold text-gray-900 leading-tight">
                        Acier Inoxydable
                    </h3>
                    <span class="font-sans text-sm text-gray-500 mt-1">
                        Résistant à l'eau
                    </span>
                </div>
            </div>

            <div class="flex items-center gap-4 w-[calc(50%-1rem)] min-[1100px]:w-auto min-[1100px]:flex-1">
                <div class="flex h-16 w-16 flex-shrink-0 items-center justify-center rounded-full border border-gray-900 bg-white text-gray-900">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 21h18v-8a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2z"></path>
                        <path d="M9 10a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2"></path>
                    </svg>
                </div>
                <div class="flex flex-col">
                    <h3 class=" text-md font-bold text-gray-900 leading-tight">
                        Click & Collect
                    </h3>
                    <span class="font-sans text-sm text-gray-500 mt-1">
                        Retrait en boutique
                    </span>
                </div>
            </div>

        </div>
    </div>
</div>

{!! view_render_event('bagisto.shop.layout.features.after') !!}
