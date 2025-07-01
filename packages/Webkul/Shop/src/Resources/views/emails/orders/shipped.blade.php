@component('shop::emails.layout')
    {{-- Main Title --}}
    <div style="margin-bottom: 20px;">
        <p style="font-size: 22px;font-weight: 600;color: #121A26; line-height: 1.2;">
            @lang('shop::app.emails.orders.shipped.title')
        </p>
    </div>

    {{-- Greeting --}}
    <div style="margin-bottom: 15px;">
        <p style="font-size: 16px;color: #5E5E5E;line-height: 24px;">
            @lang('shop::app.emails.dear', ['customer_name' => $shipment->order->customer_full_name]),👋
        </p>
        <p style="font-size: 16px;color: #5E5E5E;line-height: 24px;">
            Merci pour votre commande n°{{ $shipment->order->increment_id }}, passée le
            <strong>{{ \Carbon\Carbon::parse($shipment->order->created_at)->translatedFormat('l j F') }}</strong>.
        </p>
    </div>

    {{-- Summary Title --}}
    <div style="margin-bottom: 20px;">
        <p style="font-size: 20px;font-weight: 600;color: #121A26">
            @lang('shop::app.emails.orders.shipped.summary')
        </p>
    </div>

    {{-- Style pour la responsivité --}}
    <style type="text/css">
        @media screen and (max-width: 600px) {
            .stack-column {
                width: 100% !important;
                display: block !important;
                padding: 0 !important;
            }
            .stack-column-first {
                padding-bottom: 25px !important;
            }
        }
    </style>

    {{-- Adresses & Infos Expédition --}}
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 20px;">
        <tbody>
        <tr>
            @if ($shipment->order->shipping_address)
                {{-- ============================================ --}}
                {{-- COLONNE DE GAUCHE : LIVRAISON              --}}
                {{-- ============================================ --}}
                <td class="stack-column stack-column-first" width="50%" valign="top" style="padding-right: 20px;">
                    <table width="100%" cellpadding="0" cellspacing="0" border="0">
                        <tbody>
                        <!-- Bloc Adresse de livraison -->
                        <tr>
                            <td style="padding-bottom: 25px;">
                                <h4 style="font-family: sans-serif; font-size: 16px; font-weight: 600; color: #121A26; margin: 0 0 10px 0;">
                                    @lang('shop::app.emails.orders.shipping-address')
                                </h4>
                                <p style="font-family: sans-serif; font-size: 15px; font-weight: 400; color: #384860; line-height: 24px; margin: 0;">
                                    @if ($shipment->order->shipping_address->company_name)
                                        {{ $shipment->order->shipping_address->company_name }}<br/>
                                    @endif
                                    {{ $shipment->order->shipping_address->name }}<br/>
                                    {{-- Note: J'utilise address1, plus standard que address --}}
                                    {{ $shipment->order->shipping_address->address1 }}<br/>
                                    @if ($shipment->order->shipping_address->address2)
                                        {{ $shipment->order->shipping_address->address2 }}<br/>
                                    @endif
                                    {{ $shipment->order->shipping_address->postcode }} {{ $shipment->order->shipping_address->city }}<br/>
                                    {{ core()->country_name($shipment->order->shipping_address->country) }}<br/>
                                    <span style="color: #6E6E6E;">@lang('shop::app.emails.orders.contact') : {{ $shipment->order->shipping_address->phone }}</span>
                                </p>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </td>
            @endif

            @if ($shipment->order->billing_address)
                {{-- ============================================ --}}
                {{-- COLONNE DE DROITE : FACTURATION            --}}
                {{-- ============================================ --}}
                <td class="stack-column" width="50%" valign="top" style="padding-left: 20px;">
                    <table width="100%" cellpadding="0" cellspacing="0" border="0">
                        <tbody>
                        <!-- Bloc Adresse de facturation -->
                        <tr>
                            <td style="padding-bottom: 25px;">
                                <h4 style="font-family: sans-serif; font-size: 16px; font-weight: 600; color: #121A26; margin: 0 0 10px 0;">
                                    @lang('shop::app.emails.orders.billing-address')
                                </h4>
                                <p style="font-family: sans-serif; font-size: 15px; font-weight: 400; color: #384860; line-height: 24px; margin: 0;">
                                    @if ($shipment->order->billing_address->company_name)
                                        {{ $shipment->order->billing_address->company_name }}<br/>
                                    @endif
                                    {{ $shipment->order->billing_address->name }}<br/>

                                    {{ $shipment->order->billing_address->address1 }}<br/>
                                    @if ($shipment->order->billing_address->address2)
                                        {{ $shipment->order->billing_address->address2 }}<br/>
                                    @endif
                                    {{ $shipment->order->billing_address->postcode }} {{ $shipment->order->billing_address->city }}<br/>
                                    {{ core()->country_name($shipment->order->billing_address->country) }}<br/>
                                    <span style="color: #6E6E6E;">@lang('shop::app.emails.orders.contact') : {{ $shipment->order->billing_address->phone }}</span>
                                </p>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </td>
            @endif
        </tr>
        </tbody>
    </table>

    {{-- Section Méthode d'expédition - Full Width --}}
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top: 25px; margin-bottom: 25px;">
        <tbody>
        <tr>
            <td align="center">
                {{-- Titre de la section --}}
                <p style="font-size: 18px; font-weight: 600; color: #121A26; margin-top: 0; margin-bottom: 15px; border-top: 1px solid #e0e0e0; padding-top: 25px;">
                    @lang('shop::app.emails.orders.shipping')
                </p>

                {{-- Détails de l'expédition --}}
                <div style="font-size: 16px; color: #384860; line-height: 24px;">
                    <p style="margin-top: 0; margin-bottom: 5px;">
                        {{ $shipment->order->shipping_title }}
                    </p>
                    <p style="margin-top: 0; margin-bottom: 5px;">
                        @lang('shop::app.emails.orders.carrier') :
                        <strong style="color: #121A26;">{{ $shipment->carrier_title }}</strong>
                    </p>
                    <p style="margin-top: 0; margin-bottom: 20px;">
                        Numéro de suivi :
                        <strong style="color: #121A26;">{{ $shipment->track_number }}</strong>
                    </p>
                </div>

                {{-- Bouton de suivi centré --}}
                <table width="100%" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td align="center" style="padding-top: 10px; padding-bottom: 10px;">
                            @if ($shipment->carrier_title == 'Mondial Relay' && $shipment->track_number && $shipment->order->shipping_address->postcode)
                                {{-- Bouton de suivi direct pour Mondial Relay --}}
                                <a href="https://www.mondialrelay.fr/suivi-de-colis/?numeroExpedition={{ $shipment->track_number }}&codePostal={{ $shipment->order->shipping_address->postcode }}"
                                   target="_blank"
                                   style="font-size: 15px; font-weight: 600; color: #FFFFFF; text-decoration: none; display: inline-block; padding: 14px 28px; border-radius: 4px; background-color: #2969FF;">
                                    Suivre ma commande
                                </a>
                            @else
                                {{-- Fallback : Lien générique ou simple affichage si les infos manquent --}}
                                <a href="#" target="_blank"
                                   style="font-size: 15px; font-weight: 600; color: #FFFFFF; text-decoration: none; display: inline-block; padding: 14px 28px; border-radius: 4px; background-color: #2969FF;">
                                    Suivre ma commande
                                </a>
                            @endif
                        </td>
                    </tr>
                </table>

            </td>
        </tr>
        </tbody>
    </table>

    {{-- Shipped Items Table --}}
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse; margin-bottom: 20px;">
        <thead style="font-size: 14px; color: #121A26; text-transform: uppercase;">
        <tr style="border-bottom: 1px solid #CBD5E1;">
            <th style="text-align: left; padding: 15px;">@lang('shop::app.emails.orders.sku')</th>
            <th style="text-align: left; padding: 15px;">@lang('shop::app.emails.orders.name')</th>
            <th style="text-align: right; padding: 15px;">@lang('shop::app.emails.orders.qty')</th>
        </tr>
        </thead>
        <tbody style="font-size: 16px; color: #384860;">
        @foreach ($shipment->items as $item)
            <tr style="border-bottom: 1px solid #E2E8F0;">
                <td style="padding: 15px;" valign="top">{{ $item->sku }}</td>
                <td style="padding: 15px;" valign="top">
                    {{ $item->name }}
                    @if (isset($item->additional['attributes']))
                        <div style="font-size: 14px; color: #5E5E5E;">
                            @foreach ($item->additional['attributes'] as $attribute)
                                <b>{{ $attribute['attribute_name'] }} : </b>{{ $attribute['option_label'] }}<br/>
                            @endforeach
                        </div>
                    @endif
                </td>
                <td style="padding: 15px; text-align: right;" valign="top">{{ $item->qty }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

@endcomponent
