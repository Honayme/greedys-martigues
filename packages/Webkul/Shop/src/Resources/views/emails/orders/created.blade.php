@component('shop::emails.layout')
    {{-- Main Title --}}
    <div style="margin-bottom: 20px;">
        <p style="font-size: 22px;font-weight: 600;color: #121A26; line-height: 1.2;">
            @lang('shop::app.emails.orders.created.title')
        </p>
    </div>

    {{-- Greeting --}}
    <div style="margin-bottom: 15px;">
        <p style="font-size: 16px;color: #5E5E5E;line-height: 24px;">
            @lang('shop::app.emails.dear', ['customer_name' => $order->customer_full_name]),👋
        </p>

        {{-- Simplified greeting without customer account link --}}
        <p style="font-size: 16px;color: #5E5E5E;line-height: 24px;">
            Merci pour votre commande n°{{ $order->increment_id }}, passée le
            <strong>{{ \Carbon\Carbon::parse($order->created_at)->translatedFormat('l j F') }}</strong>.
            Nous vous enverrons un autre e-mail dès qu'elle sera expédiée.
        </p>
    </div>

    {{-- Summary Title --}}
    <div style="margin-bottom: 20px;">
        <p style="font-size: 20px;font-weight: 600;color: #121A26">
            @lang('shop::app.emails.orders.created.summary')
        </p>
    </div>

    {{-- Addresses & Shipping/Payment Info --}}
    {{-- Adresses & Infos Expédition/Paiement (Structure améliorée) --}}
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 20px;">
        <tbody>
        <tr>
            {{-- ============================================ --}}
            {{-- COLONNE DE GAUCHE : LIVRAISON & EXPÉDITION   --}}
            {{-- ============================================ --}}
            @if ($order->shipping_address)
                <td width="50%" valign="top" style="padding-right: 20px;">
                    <table width="100%" cellpadding="0" cellspacing="0" border="0">
                        <tbody>
                        {{-- Bloc Adresse de livraison --}}
                        <tr>
                            <td style="padding-bottom: 25px;">
                                <p style="font-size: 16px; font-weight: 600; color: #121A26; margin: 0 0 10px 0;">
                                    @lang('shop::app.emails.orders.shipping-address')
                                </p>
                                <p style="font-size: 15px; font-weight: 400; color: #384860; line-height: 24px; margin: 0;">
                                    @if ($order->shipping_address->company_name)
                                        {{ $order->shipping_address->company_name }}<br/>
                                    @endif
                                    {{ $order->shipping_address->name }}<br/>
                                    {{ $order->shipping_address->address1 }}<br/>
                                    @if ($order->shipping_address->address2)
                                        {{ $order->shipping_address->address2 }}<br/>
                                    @endif
                                    {{ $order->shipping_address->postcode }} {{ $order->shipping_address->city }}<br/>
                                    {{ core()->country_name($order->shipping_address->country) }}<br/>
                                    ---<br/>
                                    <span style="color: #6E6E6E;">@lang('shop::app.emails.orders.contact') : {{ $order->shipping_address->phone }}</span>
                                </p>
                            </td>
                        </tr>

                        {{-- Bloc Méthode d'expédition --}}
                        <tr>
                            <td>
                                <p style="font-size: 16px; font-weight: 600; color: #121A26; margin: 0 0 10px 0;">
                                    @lang('shop::app.emails.orders.shipping')
                                </p>
                                <p style="font-size: 15px; font-weight: 400; color: #384860; line-height: 24px; margin: 0;">
                                    {{ $order->shipping_title }}
                                </p>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </td>
            @endif

            {{-- ============================================ --}}
            {{-- COLONNE DE DROITE : FACTURATION & PAIEMENT --}}
            {{-- ============================================ --}}
            @if ($order->billing_address)
                <td width="50%" valign="top" style="padding-left: 20px;">
                    <table width="100%" cellpadding="0" cellspacing="0" border="0">
                        <tbody>
                        {{-- Bloc Adresse de facturation --}}
                        <tr>
                            <td style="padding-bottom: 25px;">
                                <p style="font-size: 16px; font-weight: 600; color: #121A26; margin: 0 0 10px 0;">
                                    @lang('shop::app.emails.orders.billing-address')
                                </p>
                                <p style="font-size: 15px; font-weight: 400; color: #384860; line-height: 24px; margin: 0;">
                                    @if ($order->billing_address->company_name)
                                        {{ $order->billing_address->company_name }}<br/>
                                    @endif
                                    {{ $order->billing_address->name }}<br/>
                                    {{ $order->billing_address->address1 }}<br/>
                                    @if ($order->billing_address->address2)
                                        {{ $order->billing_address->address2 }}<br/>
                                    @endif
                                    {{ $order->billing_address->postcode }} {{ $order->billing_address->city }}<br/>
                                    {{ core()->country_name($order->billing_address->country) }}<br/>
                                    ---<br/>
                                    <span style="color: #6E6E6E;">@lang('shop::app.emails.orders.contact') : {{ $order->billing_address->phone }}</span>
                                </p>
                            </td>
                        </tr>

                        {{-- Bloc Méthode de paiement --}}
                        <tr>
                            <td>
                                <p style="font-size: 16px; font-weight: 600; color: #121A26; margin: 0 0 10px 0;">
                                    @lang('shop::app.emails.orders.payment')
                                </p>
                                <p style="font-size: 15px; font-weight: 400; color: #384860; line-height: 24px; margin: 0;">
                                    {{ core()->getConfigData('sales.payment_methods.' . $order->payment->method . '.title') }}

                                    @php $additionalDetails = \Webkul\Payment\Payment::getAdditionalDetails($order->payment->method); @endphp
                                    @if (! empty($additionalDetails))
                                        <br/><span style="font-size: 14px; color: #5E5E5E; line-height: 20px;">{{ $additionalDetails['title'] }}: {{ $additionalDetails['value'] }}</span>
                                    @endif
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

    {{-- Items Table --}}
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse; margin-bottom: 20px;">
        <thead style="font-size: 14px; color: #121A26; text-transform: uppercase; border-top: 1px solid #CBD5E1; border-bottom: 1px solid #CBD5E1;">
        <tr>
            <th style="text-align: left; padding: 15px;">@lang('shop::app.emails.orders.sku')</th>
            <th style="text-align: left; padding: 15px;">@lang('shop::app.emails.orders.name')</th>
            <th style="text-align: right; padding: 15px;">@lang('shop::app.emails.orders.price')</th>
            <th style="text-align: right; padding: 15px;">@lang('shop::app.emails.orders.qty')</th>
        </tr>
        </thead>
        <tbody style="font-size: 16px; color: #384860;">
        @foreach ($order->items as $item)
            <tr style="border-bottom: 1px solid #E2E8F0;">
                <td style="padding: 15px;" valign="top">
                    {{ $item->getTypeInstance()->getOrderedItem($item)->sku }}
                </td>
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
                <td style="padding: 15px; text-align: right;" valign="top">
                    @if (core()->getConfigData('sales.taxes.sales.display_prices') == 'including_tax')
                        {{ core()->formatPrice($item->price_incl_tax, $order->order_currency_code) }}
                    @elseif (core()->getConfigData('sales.taxes.sales.display_prices') == 'both')
                        {{ core()->formatPrice($item->price_incl_tax, $order->order_currency_code) }}
                        <br/>
                        <span style="font-size: 12px; white-space: nowrap">
                                (@lang('shop::app.emails.orders.excl-tax')
                            {{ core()->formatPrice($item->price, $order->order_currency_code) }})
                            </span>
                    @else
                        {{ core()->formatPrice($item->price, $order->order_currency_code) }}
                    @endif
                </td>
                <td style="padding: 15px; text-align: right;" valign="top">
                    {{ $item->qty_ordered }}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    {{-- Totals --}}
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top: 20px;">
        <tr>
            <td width="50%"></td>
            <td width="50%">
                <table width="100%" cellpadding="5" cellspacing="0" border="0" style="font-size: 16px; color: #384860;">
                    {{-- Subtotal --}}
                    @if (core()->getConfigData('sales.taxes.sales.display_subtotal') == 'including_tax')
                        <tr>
                            <td>@lang('shop::app.emails.orders.subtotal-incl-tax')</td>
                            <td align="right">{{ core()->formatPrice($order->sub_total_incl_tax, $order->order_currency_code) }}</td>
                        </tr>
                    @elseif (core()->getConfigData('sales.taxes.sales.display_subtotal') == 'both')
                        <tr>
                            <td>@lang('shop::app.emails.orders.subtotal-excl-tax')</td>
                            <td align="right">{{ core()->formatPrice($order->sub_total, $order->order_currency_code) }}</td>
                        </tr>
                        <tr>
                            <td>@lang('shop::app.emails.orders.subtotal-incl-tax')</td>
                            <td align="right">{{ core()->formatPrice($order->sub_total_incl_tax, $order->order_currency_code) }}</td>
                        </tr>
                    @else
                        <tr>
                            <td>@lang('shop::app.emails.orders.subtotal-excl-tax')</td>
                            <td align="right">{{ core()->formatPrice($order->sub_total, $order->order_currency_code) }}</td>
                        </tr>
                    @endif

                    {{-- Shipping --}}
                    @if ($order->shipping_address)
                        @if (core()->getConfigData('sales.taxes.sales.display_shipping_amount') == 'including_tax')
                            <tr>
                                <td>@lang('shop::app.emails.orders.shipping-handling-incl-tax')</td>
                                <td align="right">{{ core()->formatPrice($order->shipping_amount_incl_tax, $order->order_currency_code) }}</td>
                            </tr>
                        @elseif (core()->getConfigData('sales.taxes.sales.display_shipping_amount') == 'both')
                            <tr>
                                <td>@lang('shop::app.emails.orders.shipping-handling-excl-tax')</td>
                                <td align="right">{{ core()->formatPrice($order->shipping_amount, $order->order_currency_code) }}</td>
                            </tr>
                            <tr>
                                <td>@lang('shop::app.emails.orders.shipping-handling-incl-tax')</td>
                                <td align="right">{{ core()->formatPrice($order->shipping_amount_incl_tax, $order->order_currency_code) }}</td>
                            </tr>
                        @else
                            <tr>
                                <td>@lang('shop::app.emails.orders.shipping-handling-excl-tax')</td>
                                <td align="right">{{ core()->formatPrice($order->shipping_amount, $order->order_currency_code) }}</td>
                            </tr>
                        @endif
                    @endif

                    {{-- Tax --}}
                    @if ($order->tax_amount > 0)
                        <tr>
                            <td>@lang('shop::app.emails.orders.tax')</td>
                            <td align="right">{{ core()->formatPrice($order->tax_amount, $order->order_currency_code) }}</td>
                        </tr>
                    @endif

                    {{-- Discount --}}
                    @if ($order->discount_amount > 0)
                        <tr>
                            <td>@lang('shop::app.emails.orders.discount')</td>
                            <td align="right">-{{ core()->formatPrice($order->discount_amount, $order->order_currency_code) }}</td>
                        </tr>
                    @endif

                    {{-- Grand Total --}}
                    <tr style="font-weight: bold; border-top: 1px solid #CBD5E1;">
                        <td style="padding-top: 10px;">@lang('shop::app.emails.orders.grand-total')</td>
                        <td style="padding-top: 10px; text-align: right;">{{ core()->formatPrice($order->grand_total, $order->order_currency_code) }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
@endcomponent
