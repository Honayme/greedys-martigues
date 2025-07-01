@component('shop::emails.layout')
    {{-- Main Title --}}
    <div style="margin-bottom: 20px;">
        <p style="font-size: 22px;font-weight: 600;color: #121A26; line-height: 1.2;">
            @lang('shop::app.emails.orders.invoiced.title')
        </p>
    </div>

    {{-- Greeting --}}
    <div style="margin-bottom: 15px;">
        <p style="font-size: 16px;color: #5E5E5E;line-height: 24px;">
            @lang('shop::app.emails.dear', ['customer_name' => $invoice->order->customer_full_name]),👋
        </p>
        <p style="font-size: 16px;color: #5E5E5E;line-height: 24px;">
            Merci pour Votre commande n°{{ $invoice->order->increment_id }} passée le
            <strong>{{ \Carbon\Carbon::parse($invoice->created_at)->translatedFormat('l j F') }}</strong>.
        </p>
    </div>

    {{-- Summary Title --}}
    <div style="margin-bottom: 20px;">
        <p style="font-size: 20px;font-weight: 600;color: #121A26">
            @lang('shop::app.emails.orders.invoiced.summary')
        </p>
    </div>

    {{-- Adresses & Infos Expédition/Paiement (Structure améliorée) --}}
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 20px;">
        <tbody>
        <tr>
            {{-- ============================================ --}}
            {{-- COLONNE DE GAUCHE : LIVRAISON & EXPÉDITION   --}}
            {{-- ============================================ --}}
            @if ($invoice->order->shipping_address)
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
                                    @if ($invoice->order->shipping_address->company_name)
                                        {{ $invoice->order->shipping_address->company_name }}<br/>
                                    @endif
                                    {{ $invoice->order->shipping_address->name }}<br/>
                                    {{ $invoice->order->shipping_address->address1 }}<br/>
                                    @if ($invoice->order->shipping_address->address2)
                                        {{ $invoice->order->shipping_address->address2 }}<br/>
                                    @endif
                                    {{ $invoice->order->shipping_address->postcode }} {{ $invoice->order->shipping_address->city }}<br/>
                                    {{ core()->country_name($invoice->order->shipping_address->country) }}<br/>
                                    ---<br/>
                                    <span style="color: #6E6E6E;">@lang('shop::app.emails.orders.contact') : {{ $invoice->order->shipping_address->phone }}</span>
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
                                    {{ $invoice->order->shipping_title }}
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
            @if ($invoice->order->billing_address)
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
                                    @if ($invoice->order->billing_address->company_name)
                                        {{ $invoice->order->billing_address->company_name }}<br/>
                                    @endif
                                    {{ $invoice->order->billing_address->name }}<br/>
                                    {{ $invoice->order->billing_address->address1 }}<br/>
                                    @if ($invoice->order->billing_address->address2)
                                        {{ $invoice->order->billing_address->address2 }}<br/>
                                    @endif
                                    {{ $invoice->order->billing_address->postcode }} {{ $invoice->order->billing_address->city }}<br/>
                                    {{ core()->country_name($invoice->order->billing_address->country) }}<br/>
                                    ---<br/>
                                    <span style="color: #6E6E6E;">@lang('shop::app.emails.orders.contact') : {{ $invoice->order->billing_address->phone }}</span>
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
                                    {{ core()->getConfigData('sales.payment_methods.' . $invoice->order->payment->method . '.title') }}

                                    @php $additionalDetails = \Webkul\Payment\Payment::getAdditionalDetails($invoice->order->payment->method); @endphp
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
    <div style="padding-bottom: 20px; border-bottom: 1px solid #CBD5E1;">
        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse;">
            <thead>
            <tr style="border-bottom: 1px solid #CBD5E1; color: #121A26;">
                <th style="text-align: left; padding: 15px;">@lang('shop::app.emails.orders.sku')</th>
                <th style="text-align: left; padding: 15px;">@lang('shop::app.emails.orders.name')</th>
                <th style="text-align: left; padding: 15px;">@lang('shop::app.emails.orders.price')</th>
                <th style="text-align: left; padding: 15px;">@lang('shop::app.emails.orders.qty')</th>
            </tr>
            </thead>
            <tbody style="font-size: 16px; color: #384860;">
            @foreach ($invoice->items as $item)
                <tr style="vertical-align: top; border-bottom: 1px solid #E2E8F0;">
                    <td style="padding: 15px;">{{ $item->getTypeInstance()->getOrderedItem($item)->sku }}</td>
                    <td style="padding: 15px;">
                        {{ $item->name }}
                        @if (isset($item->additional['attributes']))
                            <div style="font-size: 14px; color: #5E5E5E; margin-top: 5px;">
                                @foreach ($item->additional['attributes'] as $attribute)
                                    <b>{{ $attribute['attribute_name'] }} : </b>{{ $attribute['option_label'] }}<br/>
                                @endforeach
                            </div>
                        @endif
                    </td>
                    <td style="padding: 15px;">
                        @if (core()->getConfigData('sales.taxes.sales.display_prices') == 'including_tax')
                            {{ core()->formatPrice($item->price_incl_tax, $invoice->order_currency_code) }}
                        @elseif (core()->getConfigData('sales.taxes.sales.display_prices') == 'both')
                            {{ core()->formatPrice($item->price_incl_tax, $invoice->order_currency_code) }}
                            <div style="font-size: 12px; white-space: nowrap;">
                                @lang('shop::app.emails.orders.excl-tax')
                                    <span style="font-weight: 600;">{{ core()->formatPrice($item->price, $invoice->order_currency_code) }}</span>
                            </div>
                        @else
                            {{ core()->formatPrice($item->price, $invoice->order_currency_code) }}
                        @endif
                    </td>
                    <td style="padding: 15px;">{{ $item->qty }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    {{-- Totals --}}
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top: 20px;">
        <tr>
            {{-- Empty cell for spacing on the left --}}
            <td width="60%"></td>

            {{-- Totals content cell on the right --}}
            <td width="40%">
                <table width="100%" cellpadding="0" cellspacing="0" border="0" style="font-size: 16px; color: #384860;">
                    {{-- Subtotal --}}
                    @if (core()->getConfigData('sales.taxes.sales.display_subtotal') == 'including_tax' || core()->getConfigData('sales.taxes.sales.display_subtotal') == 'both')
                        <tr>
                            <td style="padding: 10px 0;">@lang('shop::app.emails.orders.subtotal-incl-tax')</td>
                            <td style="padding: 10px 0; text-align: right;">{{ core()->formatPrice($invoice->sub_total_incl_tax, $invoice->order_currency_code) }}</td>
                        </tr>
                    @endif
                    @if (core()->getConfigData('sales.taxes.sales.display_subtotal') == 'excluding_tax' || core()->getConfigData('sales.taxes.sales.display_subtotal') == 'both')
                        <tr>
                            <td style="padding: 10px 0;">@lang('shop::app.emails.orders.subtotal-excl-tax')</td>
                            <td style="padding: 10px 0; text-align: right;">{{ core()->formatPrice($invoice->sub_total, $invoice->order_currency_code) }}</td>
                        </tr>
                    @endif

                    {{-- Shipping --}}
                    @if ($invoice->order->shipping_address)
                        @if (core()->getConfigData('sales.taxes.sales.display_shipping_amount') == 'including_tax' || core()->getConfigData('sales.taxes.sales.display_shipping_amount') == 'both')
                            <tr>
                                <td style="padding: 10px 0;">@lang('shop::app.emails.orders.shipping-handling-incl-tax')</td>
                                <td style="padding: 10px 0; text-align: right;">{{ core()->formatPrice($invoice->shipping_amount_incl_tax, $invoice->order_currency_code) }}</td>
                            </tr>
                        @endif
                        @if (core()->getConfigData('sales.taxes.sales.display_shipping_amount') == 'excluding_tax' || core()->getConfigData('sales.taxes.sales.display_shipping_amount') == 'both')
                            <tr>
                                <td style="padding: 10px 0;">@lang('shop::app.emails.orders.shipping-handling-excl-tax')</td>
                                <td style="padding: 10px 0; text-align: right;">{{ core()->formatPrice($invoice->shipping_amount, $invoice->order_currency_code) }}</td>
                            </tr>
                        @endif
                    @endif

                    {{-- Tax --}}
                    <tr>
                        <td style="padding: 10px 0;">@lang('shop::app.emails.orders.tax')</td>
                        <td style="padding: 10px 0; text-align: right;">{{ core()->formatPrice($invoice->tax_amount, $invoice->order_currency_code) }}</td>
                    </tr>

                    {{-- Discount --}}
                    @if ($invoice->discount_amount > 0)
                        <tr>
                            <td style="padding: 10px 0;">@lang('shop::app.emails.orders.discount')</td>
                            <td style="padding: 10px 0; text-align: right;">-{{ core()->formatPrice($invoice->discount_amount, $invoice->order_currency_code) }}</td>
                        </tr>
                    @endif

                    {{-- Grand Total --}}
                    <tr style="font-weight: bold; border-top: 1px solid #CBD5E1;">
                        <td style="padding: 10px 0;">@lang('shop::app.emails.orders.grand-total')</td>
                        <td style="padding: 10px 0; text-align: right;">{{ core()->formatPrice($invoice->grand_total, $invoice->order_currency_code) }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

@endcomponent
