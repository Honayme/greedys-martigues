<!--
    This code needs to be refactored to reduce the amount of PHP in the Blade
    template as much as possible.
-->
@php
    $showCompare = (bool) core()->getConfigData('catalog.products.settings.compare_option');

    $showWishlist = (bool) core()->getConfigData('customer.settings.wishlist.wishlist_option');
@endphp

<div class="flex flex-wrap gap-4 px-4 pb-4 pt-6 shadow-sm lg:hidden">
    <div class="flex w-full items-center justify-between">
        <!-- Left Navigation -->
        <div class="flex items-center gap-x-1.5">
            {!! view_render_event('bagisto.shop.components.layouts.header.mobile.drawer.before') !!}

            <x-shop::drawer
                position="left"
                width="100%"
            >
                <x-slot:toggle>
                    <span class="icon-hamburger cursor-pointer text-2xl"></span>
                </x-slot>

                <x-slot:header>
                    <div class="flex items-center justify-between">
                        <a href="{{ route('shop.home.index') }}">
                            <img
                                src="{{ asset('storage/custom-pictures/greedys-creation-logo.png') }}"
                                alt="Greedy's Création Logo"
                                width="125"
                                height="35"
                            >
                        </a>
                    </div>
                </x-slot>

                <x-slot:content>
                    <!-- Account Profile Hero Section -->
                    <div class="mb-4 grid grid-cols-[auto_1fr] items-center gap-4 rounded-xl border border-zinc-200 p-2.5 max-md:mt-4">
                        <div>
                            <img
                                src="{{ auth()->user()?->image_url ??  bagisto_asset('images/user-placeholder.png') }}"
                                class="h-[60px] w-[60px] rounded-full max-md:rounded-full"
                            >
                        </div>

                        @guest('customer')
                            <a
                                href="{{ route('shop.customer.session.create') }}"
                                class="flex text-base font-medium"
                            >
                                @lang('shop::app.components.layouts.header.mobile.login')

                                <i class="icon-double-arrow text-2xl ltr:ml-2.5 rtl:mr-2.5"></i>
                            </a>
                        @endguest

                        @auth('customer')
                            <div class="flex flex-col justify-between gap-2.5 max-md:gap-0">
                                <p class="font-mediums break-all text-2xl max-md:text-xl">Hello! {{ auth()->user()?->first_name }}</p>

                                <p class="text-zinc-500 no-underline max-md:text-sm">{{ auth()->user()?->email }}</p>
                            </div>
                        @endauth
                    </div>

                    {!! view_render_event('bagisto.shop.components.layouts.header.mobile.drawer.categories.before') !!}

                    <!-- Mobile category view -->
                    <v-mobile-category></v-mobile-category>

                    {!! view_render_event('bagisto.shop.components.layouts.header.mobile.drawer.categories.after') !!}
                </x-slot>

                <x-slot:footer></x-slot>
            </x-shop::drawer>

            {!! view_render_event('bagisto.shop.components.layouts.header.mobile.drawer.after') !!}

            {!! view_render_event('bagisto.shop.components.layouts.header.mobile.logo.before') !!}

            <a
                href="{{ route('shop.home.index') }}"
                class="max-h-[30px] top-[-10px]"
                aria-label="@lang('shop::app.components.layouts.header.bagisto')"
            >
<!--                <img
                    src="{{ core()->getCurrentChannel()->logo_url ?? bagisto_asset('images/logo.svg') }}"
                    alt="{{ config('app.name') }}"
                    width="131"
                    height="29"
                >-->
                <img
                    src="{{ asset('storage/custom-pictures/greedys-creation-logo.png') }}"
                    alt="Greedy's Création Logo"
                    width="131"
                    height="29"
                >
            </a>

            {!! view_render_event('bagisto.shop.components.layouts.header.mobile.logo.after') !!}
        </div>

        <!-- Right Navigation -->
        <div>
            <div class="flex items-center gap-x-5 max-md:gap-x-4">
                <ul class="flex items-center space-x-6 text-base font-medium text-gray-700">
                    <li class="relative mx-4">
                        <a href="https://www.facebook.com/stephane.moustachatte.5" class="hover:text-gray-900 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="20" height="20" viewBox="0 0 50 50">
                                <path d="M41,4H9C6.24,4,4,6.24,4,9v32c0,2.76,2.24,5,5,5h32c2.76,0,5-2.24,5-5V9C46,6.24,43.76,4,41,4z M37,19h-2c-2.14,0-3,0.5-3,2 v3h5l-1,5h-4v15h-5V29h-4v-5h4v-3c0-4,2-7,6-7c2.9,0,4,1,4,1V19z"></path>
                            </svg>
                        </a>
                    </li>
                    <li class="relative mx-4">
                        <a href="https://www.instagram.com/kmillewardrobe/?hl=fr" class="hover:text-gray-900 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="20" height="20" viewBox="0 0 50 50">
                                <path d="M 16 3 C 8.8324839 3 3 8.8324839 3 16 L 3 34 C 3 41.167516 8.8324839 47 16 47 L 34 47 C 41.167516 47 47 41.167516 47 34 L 47 16 C 47 8.8324839 41.167516 3 34 3 L 16 3 z M 16 5 L 34 5 C 40.086484 5 45 9.9135161 45 16 L 45 34 C 45 40.086484 40.086484 45 34 45 L 16 45 C 9.9135161 45 5 40.086484 5 34 L 5 16 C 5 9.9135161 9.9135161 5 16 5 z M 37 11 A 2 2 0 0 0 35 13 A 2 2 0 0 0 37 15 A 2 2 0 0 0 39 13 A 2 2 0 0 0 37 11 z M 25 14 C 18.936712 14 14 18.936712 14 25 C 14 31.063288 18.936712 36 25 36 C 31.063288 36 36 31.063288 36 25 C 36 18.936712 31.063288 14 25 14 z M 25 16 C 29.982407 16 34 20.017593 34 25 C 34 29.982407 29.982407 34 25 34 C 20.017593 34 16 29.982407 16 25 C 16 20.017593 20.017593 16 25 16 z"></path>
                            </svg>
                        </a>
                    </li>
                    <li class="relative mx-4">
                        <a href="https://www.tiktok.com/@kmillewardrobe" class="hover:text-gray-900 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="20" height="20" viewBox="0 0 50 50">
                                <path d="M41,4H9C6.243,4,4,6.243,4,9v32c0,2.757,2.243,5,5,5h32c2.757,0,5-2.243,5-5V9C46,6.243,43.757,4,41,4z M37.006,22.323 c-0.227,0.021-0.457,0.035-0.69,0.035c-2.623,0-4.928-1.349-6.269-3.388c0,5.349,0,11.435,0,11.537c0,4.709-3.818,8.527-8.527,8.527 s-8.527-3.818-8.527-8.527s3.818-8.527,8.527-8.527c0.178,0,0.352,0.016,0.527,0.027v4.202c-0.175-0.021-0.347-0.053-0.527-0.053 c-2.404,0-4.352,1.948-4.352,4.352s1.948,4.352,4.352,4.352s4.527-1.894,4.527-4.298c0-0.095,0.042-19.594,0.042-19.594h4.016 c0.378,3.591,3.277,6.425,6.901,6.685V22.323z"></path>
                            </svg>
                        </a>
                    </li>
                </ul>

                {!! view_render_event('bagisto.shop.components.layouts.header.mobile.compare.before') !!}

                @if($showCompare)
                    <a
                        href="{{ route('shop.compare.index') }}"
                        aria-label="@lang('shop::app.components.layouts.header.compare')"
                    >
                        <span class="icon-compare cursor-pointer text-2xl"></span>
                    </a>
                @endif

                {!! view_render_event('bagisto.shop.components.layouts.header.mobile.compare.after') !!}

                {!! view_render_event('bagisto.shop.components.layouts.header.mobile.mini_cart.before') !!}

                @if(core()->getConfigData('sales.checkout.shopping_cart.cart_page'))
                    @include('shop::checkout.cart.mini-cart')
                @endif

                {!! view_render_event('bagisto.shop.components.layouts.header.mobile.mini_cart.after') !!}

                <!-- For Large screens -->
                <div class="max-md:hidden">
                    <x-shop::dropdown position="bottom-{{ core()->getCurrentLocale()->direction === 'ltr' ? 'right' : 'left' }}">
                        <x-slot:toggle>
                            <span class="icon-users cursor-pointer text-2xl"></span>
                        </x-slot>

                        <!-- Guest Dropdown -->
                        @guest('customer')
                            <x-slot:content>
                                <div class="grid gap-2.5">
                                    <p class="font-dmserif text-xl">
                                        @lang('shop::app.components.layouts.header.welcome-guest')
                                    </p>

                                    <p class="text-sm">
                                        @lang('shop::app.components.layouts.header.dropdown-text')
                                    </p>
                                </div>

                                <p class="mt-3 w-full border border-zinc-200"></p>

                                {!! view_render_event('bagisto.shop.components.layouts.header.mobile.index.customers_action.before') !!}

                                <div class="mt-6 flex gap-4">
                                    {!! view_render_event('bagisto.shop.components.layouts.header.mobile.index.sign_in_button.before') !!}

                                    <a
                                        href="{{ route('shop.customer.session.create') }}"
                                        class="m-0 mx-auto block w-max cursor-pointer rounded-2xl bg-navyBlue px-7 py-4 text-center text-base font-medium text-white ltr:ml-0 rtl:mr-0"
                                    >
                                        @lang('shop::app.components.layouts.header.sign-in')
                                    </a>

                                    <a
                                        href="{{ route('shop.customers.register.index') }}"
                                        class="m-0 mx-auto block w-max cursor-pointer rounded-2xl border-2 border-navyBlue bg-white px-7 py-3.5 text-center text-base font-medium text-navyBlue ltr:ml-0 rtl:mr-0"
                                    >
                                        @lang('shop::app.components.layouts.header.sign-up')
                                    </a>

                                    {!! view_render_event('bagisto.shop.components.layouts.header.mobile.index.sign_in_button.after') !!}
                                </div>

                                {!! view_render_event('bagisto.shop.components.layouts.header.mobile.index.customers_action.after') !!}
                            </x-slot>
                        @endguest

                        <!-- Customers Dropdown -->
                        @auth('customer')
                            <x-slot:content class="!p-0">
                                <div class="grid gap-2.5 p-5 pb-0">
                                    <p class="font-dmserif text-xl">
                                        @lang('shop::app.components.layouts.header.welcome')’
                                        {{ auth()->guard('customer')->user()->first_name }}
                                    </p>

                                    <p class="text-sm">
                                        @lang('shop::app.components.layouts.header.dropdown-text')
                                    </p>
                                </div>

                                <p class="mt-3 w-full border border-zinc-200"></p>

                                <div class="mt-2.5 grid gap-1 pb-2.5">
                                    {!! view_render_event('bagisto.shop.components.layouts.header.mobile.index.profile_dropdown.links.before') !!}

                                    <a
                                        class="cursor-pointer px-5 py-2 text-base hover:bg-gray-100"
                                        href="{{ route('shop.customers.account.profile.index') }}"
                                    >
                                        @lang('shop::app.components.layouts.header.profile')
                                    </a>

                                    <a
                                        class="cursor-pointer px-5 py-2 text-base hover:bg-gray-100"
                                        href="{{ route('shop.customers.account.orders.index') }}"
                                    >
                                        @lang('shop::app.components.layouts.header.orders')
                                    </a>

                                    @if ($showWishlist)
                                        <a
                                            class="cursor-pointer px-5 py-2 text-base hover:bg-gray-100"
                                            href="{{ route('shop.customers.account.wishlist.index') }}"
                                        >
                                            @lang('shop::app.components.layouts.header.wishlist')
                                        </a>
                                    @endif

                                    <!--Customers logout-->
                                    @auth('customer')
                                        <x-shop::form
                                            method="DELETE"
                                            action="{{ route('shop.customer.session.destroy') }}"
                                            id="customerLogout"
                                        />

                                        <a
                                            class="cursor-pointer px-5 py-2 text-base hover:bg-gray-100"
                                            href="{{ route('shop.customer.session.destroy') }}"
                                            onclick="event.preventDefault(); document.getElementById('customerLogout').submit();"
                                        >
                                            @lang('shop::app.components.layouts.header.logout')
                                        </a>
                                    @endauth

                                    {!! view_render_event('bagisto.shop.components.layouts.header.mobile.index.profile_dropdown.links.after') !!}
                                </div>
                            </x-slot>
                        @endauth
                    </x-shop::dropdown>
                </div>

                <!-- For Medium and small screen -->
                <div class="md:hidden">
                    @guest('customer')
                        <a
                            href="{{ route('shop.customer.session.create') }}"
                            aria-label="@lang('shop::app.components.layouts.header.account')"
                        >
                            <span class="icon-users cursor-pointer text-2xl"></span>
                        </a>
                    @endguest

                    <!-- Customers Dropdown -->
                    @auth('customer')
                        <a
                            href="{{ route('shop.customers.account.index') }}"
                            aria-label="@lang('shop::app.components.layouts.header.account')"
                        >
                            <span class="icon-users cursor-pointer text-2xl"></span>
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </div>

    {!! view_render_event('bagisto.shop.components.layouts.header.mobile.search.before') !!}

    <!-- Serach Catalog Form -->
    <form action="{{ route('shop.search.index') }}" class="flex w-full items-center">
        <label
            for="organic-search"
            class="sr-only"
        >
            @lang('shop::app.components.layouts.header.search')
        </label>

        <div class="relative w-full">
            <div class="icon-search pointer-events-none absolute top-3 flex items-center text-2xl max-md:text-xl max-sm:top-2.5 ltr:left-3 rtl:right-3"></div>

            <input
                type="text"
                class="block w-full rounded-xl border border-['#E3E3E3'] px-11 py-3.5 text-sm font-medium text-gray-900 max-md:rounded-lg max-md:px-10 max-md:py-3 max-md:font-normal max-sm:text-xs"
                name="query"
                value="{{ request('query') }}"
                placeholder="@lang('shop::app.components.layouts.header.search-text')"
                required
            >

            @if (core()->getConfigData('catalog.products.settings.image_search'))
                @include('shop::search.images.index')
            @endif
        </div>
    </form>

    {!! view_render_event('bagisto.shop.components.layouts.header.mobile.search.after') !!}

</div>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-mobile-category-template"
    >
        <div>
            <template v-for="(category) in categories">
                {!! view_render_event('bagisto.shop.components.layouts.header.mobile.category.before') !!}

                <div class="flex items-center justify-between border border-b border-l-0 border-r-0 border-t-0 border-zinc-100 py-3.5 max-sm:py-2.5">
                    <a
                        :href="category.url"
                        class="flex items-center justify-between"
                    >
                        @{{ category.name }}
                    </a>

                    <span
                        class="cursor-pointer text-2xl"
                        :class="{'icon-arrow-down': category.isOpen, 'icon-arrow-right': ! category.isOpen}"
                        @click="toggle(category)"
                    >
                    </span>
                </div>

                <div
                    class="grid gap-2"
                    v-if="category.isOpen"
                >
                    <ul v-if="category.children.length">
                        <li v-for="secondLevelCategory in category.children">
                            <div class="flex items-center justify-between border border-b border-l-0 border-r-0 border-t-0 border-zinc-100 ltr:ml-3 rtl:mr-3">
                                <a
                                    :href="secondLevelCategory.url"
                                    class="mt-5 flex items-center justify-between pb-5"
                                >
                                    @{{ secondLevelCategory.name }}
                                </a>

                                <span
                                    class="cursor-pointer text-2xl"
                                    :class="{
                                        'icon-arrow-down': secondLevelCategory.category_show,
                                        'icon-arrow-right': ! secondLevelCategory.category_show
                                    }"
                                    @click="secondLevelCategory.category_show = ! secondLevelCategory.category_show"
                                >
                                </span>
                            </div>

                            <div v-if="secondLevelCategory.category_show">
                                <ul v-if="secondLevelCategory.children.length">
                                    <li v-for="thirdLevelCategory in secondLevelCategory.children">
                                        <div class="flex items-center justify-between border border-b border-l-0 border-r-0 border-t-0 border-zinc-100 ltr:ml-3 rtl:mr-3">
                                            <a
                                                :href="thirdLevelCategory.url"
                                                class="mt-5 flex items-center justify-between pb-5 ltr:ml-3 rtl:mr-3"
                                            >
                                                @{{ thirdLevelCategory.name }}
                                            </a>
                                        </div>
                                    </li>
                                </ul>

                                <span
                                    class="ltr:ml-2 rtl:mr-2"
                                    v-else
                                >
                                    @lang('shop::app.components.layouts.header.no-category-found')
                                </span>
                            </div>
                        </li>
                    </ul>

                    <span
                        class="mt-2 max-sm:my-1.5 ltr:ml-2 rtl:mr-2"
                        v-else
                    >
                        @lang('shop::app.components.layouts.header.no-category-found')
                    </span>
                </div>

                {!! view_render_event('bagisto.shop.components.layouts.header.mobile.category.after') !!}
            </template>
        </div>

        <!-- Localization & Currency Section -->
        @if(core()->getCurrentChannel()->locales()->count() > 1 || core()->getCurrentChannel()->currencies()->count() > 1 )
            <div class="w-full border-t bg-white">
                <div class="fixed bottom-0 z-10 grid w-full max-w-full grid-cols-[1fr_auto_1fr] items-center justify-items-center border-t border-zinc-200 bg-white px-5 ltr:left-0 rtl:right-0">
                    <!-- Filter Drawer -->
                    <x-shop::drawer
                        position="bottom"
                        width="100%"
                    >
                        <!-- Drawer Toggler -->
                        <x-slot:toggle>
                            <div
                                class="flex cursor-pointer items-center gap-x-2.5 px-2.5 py-3.5 text-lg font-medium uppercase max-md:py-3 max-sm:text-base"
                                role="button"
                            >
                                {{ core()->getCurrentCurrency()->symbol . ' ' . core()->getCurrentCurrencyCode() }}
                            </div>
                        </x-slot>

                        <!-- Drawer Header -->
                        <x-slot:header>
                            <div class="flex items-center justify-between">
                                <p class="text-lg font-semibold">
                                    @lang('shop::app.components.layouts.header.mobile.currencies')
                                </p>
                            </div>
                        </x-slot>

                        <!-- Drawer Content -->
                        <x-slot:content class="!px-0">
                            <div
                                class="overflow-auto"
                                :style="{ height: getCurrentScreenHeight }"
                            >
                                <v-currency-switcher></v-currency-switcher>
                            </div>
                        </x-slot>
                    </x-shop::drawer>

                    <!-- Seperator -->
                    <span class="h-5 w-0.5 bg-zinc-200"></span>

                    <!-- Sort Drawer -->
                    <x-shop::drawer
                        position="bottom"
                        width="100%"
                    >
                        <!-- Drawer Toggler -->
                        <x-slot:toggle>
                            <div
                                class="flex cursor-pointer items-center gap-x-2.5 px-2.5 py-3.5 text-lg font-medium uppercase max-md:py-3 max-sm:text-base"
                                role="button"
                            >
                                <img
                                    src="{{ ! empty(core()->getCurrentLocale()->logo_url)
                                            ? core()->getCurrentLocale()->logo_url
                                            : bagisto_asset('images/default-language.svg')
                                        }}"
                                    class="h-full"
                                    alt="Default locale"
                                    width="24"
                                    height="16"
                                />

                                {{ core()->getCurrentChannel()->locales()->orderBy('name')->where('code', app()->getLocale())->value('name') }}
                            </div>
                        </x-slot>

                        <!-- Drawer Header -->
                        <x-slot:header>
                            <div class="flex items-center justify-between">
                                <p class="text-lg font-semibold">
                                    @lang('shop::app.components.layouts.header.mobile.locales')
                                </p>
                            </div>
                        </x-slot>

                        <!-- Drawer Content -->
                        <x-slot:content class="!px-0">
                            <div
                                class="overflow-auto"
                                :style="{ height: getCurrentScreenHeight }"
                            >
                                <v-locale-switcher></v-locale-switcher>
                            </div>
                        </x-slot>
                    </x-shop::drawer>
                </div>
            </div>
        @endif
    </script>

    <script type="module">
        app.component('v-mobile-category', {
            template: '#v-mobile-category-template',

            data() {
                return  {
                    categories: [],
                }
            },

            mounted() {
                this.get();
            },

            computed: {
                getCurrentScreenHeight() {
                    return window.innerHeight - (window.innerWidth < 920 ? 61 : 0) + 'px';
                },
            },

            methods: {
                get() {
                    this.$axios.get("{{ route('shop.api.categories.tree') }}")
                        .then(response => {
                            this.categories = response.data.data;
                        }).catch(error => {
                            console.log(error);
                        });
                },

                toggle(selectedCategory) {
                    this.categories = this.categories.map((category) => ({
                        ...category,
                        isOpen: category.id === selectedCategory.id ? ! category.isOpen : false,
                    }));
                },
            },
        });
    </script>
@endPushOnce
