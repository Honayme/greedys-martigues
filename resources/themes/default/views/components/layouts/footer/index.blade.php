{!! view_render_event('bagisto.shop.layout.footer.before') !!}

<!--
    The category repository is injected directly here because there is no way
    to retrieve it from the view composer, as this is an anonymous component.
-->
@inject('themeCustomizationRepository', 'Webkul\Theme\Repositories\ThemeCustomizationRepository')

<!--
    This code needs to be refactored to reduce the amount of PHP in the Blade
    template as much as possible.
-->
@php
    $channel = core()->getCurrentChannel();

    $customization = $themeCustomizationRepository->findOneWhere([
        'type'       => 'footer_links',
        'status'     => 1,
        'theme_code' => $channel->theme,
        'channel_id' => $channel->id,
    ]);
@endphp

<footer class="mt-9 bg-lightOrange max-sm:mt-10">
    <div class="flex justify-between gap-x-6 gap-y-8 p-[60px] max-1060:flex-col-reverse max-md:gap-5 max-md:p-8 max-sm:px-4 max-sm:py-5">

        <!-- Logo + Nom de la marque + Map -->
        <div class="flex gap-8 max-1060:flex-col max-1060:items-center">
            <!-- Colonne gauche : Logo + Marque + Infos -->
            <div class="flex flex-col gap-6">
                <div class="flex items-center gap-4">
                    <!-- LOGO EN PNG -->
                    <img
                        src="{{ asset('themes/default/assets/images/logo.png') }}"
                        width="63"
                        height="95"
                        alt="Logo Greedy's creation boutique mode et accessoires Martigues"
                        class="footer-only"
                    >
                    <div data-ninja-font="calina_regular_normal_q2fsa" style="font-size: 40px !important; line-height: 32px !important; letter-spacing: normal !important; position: relative; top: 12px; font-family: 'Calina', cursive; font-weight: 300 !important;">
                        Greedy's<br>Création
                    </div>
                </div>
            </div>
        </div>

        <div id="greedys-map" class="flex flex-col gap-4 text-sm max-1060:items-center">
        <!-- Informations boutique -->
            <div class="flex flex-col gap-3 text-sm max-1060:text-center">
                <div class="flex items-start max-1060:w-full max-1060:justify-center">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2897.8724547845147!2d5.051744315558838!3d43.40465977913199!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x12c9ed6e5c6b7b5b%3A0x1234567890abcdef!2s17%20Rue%20Gambetta%2C%2013500%20Martigues!5e0!3m2!1sfr!2sfr!4v1234567890123!5m2!1sfr!2sfr"
                        width="800"
                        height="200"
                        style="border:3px solid white; border-radius:10px;"
                        allowfullscreen=""
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        class="w-full max-w-[800px]"
                    ></iframe>
                </div>
            </div>
        </div>


        <!-- For Desktop View -->
        <div class="flex flex-wrap items-start gap-24 max-1180:gap-6 max-1060:hidden">
            @if ($customization?->options)
                @foreach ($customization->options as $footerLinkSection)
                    <ul class="grid gap-5 text-sm">
                        @php
                            usort($footerLinkSection, function ($a, $b) {
                                return $a['sort_order'] - $b['sort_order'];
                            });
                        @endphp

                        @foreach ($footerLinkSection as $link)
                            <li>
                                <a href="{{ $link['url'] }}">
                                    {{ $link['title'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endforeach
            @endif
        </div>

        <!-- For Mobile view - Accordion pr�serv� -->
        <x-shop::accordion
            :is-active="false"
            class="hidden !w-full rounded-xl !border-2 !border-[#e9decc] max-1060:block max-sm:rounded-lg"
        >
            <x-slot:header class="rounded-t-lg bg-[#F1EADF] font-medium max-md:p-2.5 max-sm:px-3 max-sm:py-2 max-sm:text-sm">
                @lang('shop::app.components.layouts.footer.footer-content')
            </x-slot>

            <x-slot:content class="flex justify-between !bg-transparent !p-4">
                @if ($customization?->options)
                    @foreach ($customization->options as $footerLinkSection)
                        <ul class="grid gap-5 text-sm">
                            @php
                                usort($footerLinkSection, function ($a, $b) {
                                    return $a['sort_order'] - $b['sort_order'];
                                });
                            @endphp

                            @foreach ($footerLinkSection as $link)
                                <li>
                                    <a
                                        href="{{ $link['url'] }}"
                                        class="text-sm font-medium max-sm:text-xs">
                                        {{ $link['title'] }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endforeach
                @endif
            </x-slot>
        </x-shop::accordion>

        {!! view_render_event('bagisto.shop.layout.footer.newsletter_subscription.before') !!}

        <div id="greedys-info" class="flex flex-col gap-4 text-sm max-1060:items-center">
            <!-- Adresse -->
            <div class="flex items-start gap-3">
                <svg width="16" height="16" viewBox="0 0 1024 1024" fill="none" stroke="currentColor" stroke-width="40" class="flex-shrink-0" xmlns="http://www.w3.org/2000/svg">
                    <path d="M512 1012.8c-253.6 0-511.2-54.4-511.2-158.4 0-92.8 198.4-131.2 283.2-143.2h3.2c12 0 22.4 8.8 24 20.8 0.8 6.4-0.8 12.8-4.8 17.6-4 4.8-9.6 8.8-16 9.6-176.8 25.6-242.4 72-242.4 96 0 44.8 180.8 110.4 463.2 110.4s463.2-65.6 463.2-110.4c0-24-66.4-70.4-244.8-96-6.4-0.8-12-4-16-9.6-4-4.8-5.6-11.2-4.8-17.6 1.6-12 12-20.8 24-20.8h3.2c85.6 12 285.6 50.4 285.6 143.2 0.8 103.2-256 158.4-509.6 158.4z m-16.8-169.6c-12-11.2-288.8-272.8-288.8-529.6 0-168 136.8-304.8 304.8-304.8S816 145.6 816 313.6c0 249.6-276.8 517.6-288.8 528.8l-16 16-16-15.2zM512 56.8c-141.6 0-256.8 115.2-256.8 256.8 0 200.8 196 416 256.8 477.6 61.6-63.2 257.6-282.4 257.6-477.6C768.8 172.8 653.6 56.8 512 56.8z m0 392.8c-80 0-144.8-64.8-144.8-144.8S432 160 512 160c80 0 144.8 64.8 144.8 144.8 0 80-64.8 144.8-144.8 144.8zM512 208c-53.6 0-96.8 43.2-96.8 96.8S458.4 401.6 512 401.6c53.6 0 96.8-43.2 96.8-96.8S564.8 208 512 208z" />
                </svg>
                <div class="font-medium">
                    <p>17 Rue Gambetta</p>
                    <p>13500 MARTIGUES</p>
                </div>
            </div>

            <!-- Horaires -->
            <div class="flex items-center gap-3">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 32 32" class="flex-shrink-0" xmlns="http://www.w3.org/2000/svg">
                    <path d="M16 0.75c-8.422 0-15.25 6.828-15.25 15.25s6.828 15.25 15.25 15.25c8.422 0 15.25-6.828 15.25-15.25v0c-0.010-8.418-6.832-15.24-15.249-15.25h-0.001zM16 28.75c-7.042 0-12.75-5.708-12.75-12.75s5.708-12.75 12.75-12.75c7.042 0 12.75 5.708 12.75 12.75v0c-0.008 7.038-5.712 12.742-12.749 12.75h-0.001zM17.25 15.482v-9.482c0-0.69-0.56-1.25-1.25-1.25s-1.25 0.56-1.25 1.25v0 10c0 0.345 0.14 0.658 0.366 0.884l3.999 4.001c0.226 0.226 0.539 0.366 0.884 0.366 0.691 0 1.251-0.56 1.251-1.251 0-0.345-0.14-0.658-0.366-0.884l0 0z"/>
                </svg>
                <p class="font-semibold">Mar. - Sam. : 10:00 - 19:00</p>
            </div>

            <!-- Téléphone -->
            <div class="flex items-center gap-3">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="0.7" viewBox="0 0 16 16" class="flex-shrink-0" xmlns="http://www.w3.org/2000/svg">
                    <path d="M35.5,16A15.517,15.517,0,0,1,20,.5a.5.5,0,0,1,.5-.5h2.845a1.849,1.849,0,0,1,1.317.546l1.962,1.961a1.5,1.5,0,0,1,0,2.122L25.2,6.048A11.907,11.907,0,0,0,29.952,10.8l1.419-1.42a1.5,1.5,0,0,1,2.121,0l1.962,1.962A1.852,1.852,0,0,1,36,12.656V15.5A.5.5,0,0,1,35.5,16ZM21.009,1A14.52,14.52,0,0,0,35,14.992V12.656a.859.859,0,0,0-.253-.611l-1.962-1.962a.5.5,0,0,0-.707,0L30.4,11.763a.5.5,0,0,1-.578.093A12.893,12.893,0,0,1,24.145,6.18a.5.5,0,0,1,.092-.579l1.68-1.679a.5.5,0,0,0,0-.708L23.955,1.253A.858.858,0,0,0,23.345,1Z" transform="translate(-20)"/>
                </svg>
                <p class="font-medium text-base">
                    <a href="tel:0484891584" class="hover:underline">04 84 89 15 84</a>
                </p>
            </div>
        </div>
        <!-- News Letter subscription -->
        @if (core()->getConfigData('customer.settings.newsletter.subscription'))
            <div class="grid gap-2.5">
                <p
                    class="max-w-[288px] text-3xl italic leading-[45px] text-navyBlue max-md:text-2xl max-sm:text-lg"
                    role="heading"
                    aria-level="2"
                >
                    @lang('shop::app.components.layouts.footer.newsletter-text')
                </p>

                <p class="text-xs">
                    @lang('shop::app.components.layouts.footer.subscribe-stay-touch')
                </p>

                <div>
                    <x-shop::form
                        :action="route('shop.subscription.store')"
                        class="mt-2.5 rounded max-sm:mt-0"
                    >
                        <div class="relative w-full">
                            <x-shop::form.control-group.control
                                type="email"
                                class="block w-[420px] max-w-full rounded-xl border-2 border-[#e9decc] bg-[#F1EADF] px-5 py-4 text-base max-1060:w-full max-md:p-3.5 max-sm:mb-0 max-sm:rounded-lg max-sm:border-2 max-sm:p-2 max-sm:text-sm"
                                name="email"
                                rules="required|email"
                                label="Email"
                                :aria-label="trans('shop::app.components.layouts.footer.email')"
                                placeholder="email@example.com"
                            />

                            <x-shop::form.control-group.error control-name="email" />

                            <button
                                type="submit"
                                class="absolute top-1.5 flex w-max items-center rounded-xl bg-white px-7 py-2.5 font-medium hover:bg-zinc-100 max-md:top-1 max-md:px-5 max-md:text-xs max-sm:mt-0 max-sm:rounded-lg max-sm:px-4 max-sm:py-2 ltr:right-2 rtl:left-2"
                            >
                                @lang('shop::app.components.layouts.footer.subscribe')
                            </button>
                        </div>
                    </x-shop::form>
                </div>
            </div>
        @endif

        {!! view_render_event('bagisto.shop.layout.footer.newsletter_subscription.after') !!}
    </div>

    <div class="flex justify-between bg-[#F1EADF] px-[60px] py-3.5 max-md:justify-center max-sm:px-5">
        {!! view_render_event('bagisto.shop.layout.footer.footer_text.before') !!}

        <p class="text-sm text-zinc-600 max-md:text-center">
            @lang('shop::app.components.layouts.footer.footer-text', ['current_year'=> date('Y') ])
        </p>

        {!! view_render_event('bagisto.shop.layout.footer.footer_text.after') !!}
    </div>
</footer>

{!! view_render_event('bagisto.shop.layout.footer.after') !!}
