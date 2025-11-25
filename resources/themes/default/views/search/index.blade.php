@php
    $title = request()->has('query')
            ? trans('shop::app.search.title', ['query' => request()->query('query')])
            : trans('shop::app.search.results');
@endphp

@push('meta')
    <meta name="description" content="{{ $title }}"/>
    <meta name="keywords" content="{{ $title }}"/>
@endPush

<x-shop::layouts :has-feature="false">
    <x-slot:title>
        {{ $title }}
    </x-slot>

    <div class="container px-[60px] max-lg:px-8 max-sm:px-4">
        @if (request()->has('image-search'))
            @include('shop::search.images.results')
        @endif

        <div class="mt-8 flex items-center justify-between max-md:mt-5">
            <h1
                class="text-2xl font-medium max-sm:text-base"
                v-text="'{{ preg_replace('/[,\\"\\\']+/', '', $title) }}'"
            >
            </h1>
        </div>
    </div>

    <v-search>
        <x-shop::shimmer.categories.view/>
    </v-search>

    @pushOnce('scripts')
        <script type="text/x-template" id="v-search-template">
            <div class="container px-[60px] max-lg:px-8 max-sm:px-4">

                <div class="flex justify-end mb-4">
                    <button
                        @click="isDrawerActive.filter = true"
                        class="inline-flex justify-center items-center px-4 py-2 h-10 text-sm font-medium bg-white rounded-md border transition-colors hover:bg-neutral-100 active:bg-white focus:bg-white focus:outline-none focus:ring-2 focus:ring-neutral-200/60 focus:ring-offset-2 disabled:opacity-50 disabled:pointer-events-none">
                        <span>Filtrer</span>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 ml-2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 00-3 0m0 0H3.75m6.75 0a1.5 1.5 0 003 0m3.75 6h9.75m-9.75 0a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 00-3 0m0 0H3.75m6.75 0a1.5 1.5 0 003 0m-3.75 6h9.75m-9.75 0a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 00-3 0m0 0H3.75m6.75 0a1.5 1.5 0 003 0"/>
                        </svg>
                    </button>
                </div>

                <teleport to="body">
                    <div v-if="isDrawerActive.filter" class="relative z-[1001]">

                        <transition name="fade" appear>
                            <div
                                v-show="isDrawerActive.filter"
                                @click="isDrawerActive.filter = false"
                                class="fixed inset-0 custom-overlay backdrop-blur-sm"
                            ></div>
                        </transition>

                        <div class="fixed inset-0 overflow-hidden pointer-events-none">
                            <div class="absolute inset-0 overflow-hidden">
                                <div class="fixed inset-y-0 right-0 flex max-w-full pl-10 pt-0">

                                    <transition name="slide" appear>
                                        <div
                                            v-show="isDrawerActive.filter"
                                            class="pointer-events-auto w-screen max-w-[400px]"
                                        >
                                            <div class="flex h-full flex-col overflow-y-scroll bg-white shadow-2xl">

                                                <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
                                                    <h2 class="text-xl font-bold uppercase tracking-wide text-gray-900">
                                                        FILTRES
                                                    </h2>
                                                    <button
                                                        @click="isDrawerActive.filter = false"
                                                        type="button"
                                                        class="rounded-md text-gray-400 hover:text-gray-500 focus:outline-none"
                                                    >
                                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                    </button>
                                                </div>

                                                <div class="relative mt-6 flex-1 px-6">
                                                    <div id="bagisto-filters-wrapper">
                                                        @include('shop::categories.filters')
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </transition>
                                </div>
                            </div>
                        </div>
                    </div>
                </teleport>

                <div class="flex items-start gap-10 max-lg:gap-5 md:mt-10">
                    <div class="flex-1">
                        <div class="max-md:hidden">
                            @include('shop::categories.toolbar')
                        </div>

                        <div
                            class="mt-8 grid grid-cols-1 gap-6"
                            v-if="filters.toolbar.mode === 'list'"
                        >
                            <template v-if="isLoading">
                                <x-shop::shimmer.products.cards.list count="12" />
                            </template>

                            <template v-else>
                                <template v-if="products.length">
                                    <x-shop::products.card
                                        ::mode="'list'"
                                        v-for="product in products"
                                    />
                                </template>

                                <template v-else>
                                    <div class="m-auto grid w-full place-content-center items-center justify-items-center py-32 text-center">
                                        <img
                                            class="max-sm:h-[100px] max-sm:w-[100px]"
                                            src="{{ bagisto_asset('images/thank-you.png') }}"
                                            alt="Empty result"
                                        />

                                        <p class="text-xl max-sm:text-sm" role="heading">
                                            @lang('shop::app.categories.view.empty')
                                        </p>
                                    </div>
                                </template>
                            </template>
                        </div>

                        <div v-else>
                            <template v-if="isLoading">
                                <div class="mt-8 grid grid-cols-3 gap-8 max-1060:grid-cols-2 max-md:gap-x-4 max-sm:mt-5 max-sm:justify-items-center max-sm:gap-y-5">
                                    <x-shop::shimmer.products.cards.grid count="12" />
                                </div>
                            </template>

                            <template v-else>
                                <template v-if="products.length">
                                    <div class="mt-8 grid grid-cols-3 gap-8 max-1060:grid-cols-2 max-md:mt-5 max-md:justify-items-center max-md:gap-x-4 max-md:gap-y-5">
                                        <x-shop::products.card
                                            ::mode="'grid'"
                                            v-for="product in products"
                                            :navigation-link="route('shop.search.index')"
                                        />
                                    </div>
                                </template>

                                <template v-else>
                                    <div class="m-auto grid w-full place-content-center items-center justify-items-center py-32 text-center">
                                        <img
                                            class="max-sm:h-[100px] max-sm:w-[100px]"
                                            src="{{ bagisto_asset('images/thank-you.png') }}"
                                            alt="Empty result"
                                        />

                                        <p class="text-xl max-sm:text-sm" role="heading">
                                            @lang('shop::app.categories.view.empty')
                                        </p>
                                    </div>
                                </template>
                            </template>
                        </div>

                        <button
                            class="secondary-button mx-auto mt-[60px] block w-max rounded-2xl px-11 py-3 text-center text-base max-md:rounded-lg max-md:text-sm max-sm:mt-7 max-sm:px-7 max-sm:py-2"
                            @click="loadMoreProducts"
                            v-if="links.next"
                        >
                            @lang('shop::app.categories.view.load-more')
                        </button>
                    </div>
                </div>
            </div>
        </script>

        <script type="module">
            app.component('v-search', {
                template: '#v-search-template',

                data() {
                    return {
                        isMobile: window.innerWidth <= 767,
                        isLoading: true,
                        isDrawerActive: {
                            toolbar: false,
                            filter: false,
                        },
                        filters: {
                            toolbar: {},
                            filter: {},
                        },
                        products: [],
                        links: {},
                    }
                },

                computed: {
                    queryParams() {
                        let queryParams = Object.assign({}, this.filters.filter, this.filters.toolbar);
                        return this.removeJsonEmptyValues(queryParams);
                    },
                    queryString() {
                        return this.jsonToQueryString(this.queryParams);
                    },
                },

                watch: {
                    queryParams() {
                        this.getProducts();
                    },
                    queryString() {
                        window.history.pushState({}, '', '?' + this.queryString);
                    },
                },

                methods: {
                    setFilters(type, filters) {
                        this.filters[type] = filters;
                    },

                    clearFilters(type, filters) {
                        this.filters[type] = {};
                    },

                    getProducts() {
                        this.isDrawerActive.toolbar = false;

                        this.$axios.get(("{{ route('shop.api.products.index') }}"), {
                            params: this.queryParams
                        })
                            .then(response => {
                                this.isLoading = false;
                                this.products = response.data.data;
                                this.links = response.data.links;
                            }).catch(error => {
                            console.log(error);
                        });
                    },

                    loadMoreProducts() {
                        if (this.links.next) {
                            this.$axios.get(this.links.next).then(response => {
                                this.products = [...this.products, ...response.data.data];
                                this.links = response.data.links;
                            }).catch(error => {
                                console.log(error);
                            });
                        }
                    },

                    removeJsonEmptyValues(params) {
                        Object.keys(params).forEach(function (key) {
                            if ((!params[key] && params[key] !== undefined)) {
                                delete params[key];
                            }
                            if (Array.isArray(params[key])) {
                                params[key] = params[key].join(',');
                            }
                        });
                        return params;
                    },

                    jsonToQueryString(params) {
                        let parameters = new URLSearchParams();
                        for (const key in params) {
                            parameters.append(key, params[key]);
                        }
                        return parameters.toString();
                    }
                },
            });
        </script>
    @endPushOnce

    @pushOnce('styles')
        <style>
            /* 1. Force Black Overlay */
            .custom-overlay {
                background-color: rgba(0, 0, 0, 0.5) !important;
            }

            /* 2. Fade Transition (Overlay) */
            .fade-enter-active,
            .fade-leave-active {
                transition: opacity 0.5s ease-in-out;
            }
            .fade-enter-from,
            .fade-leave-to {
                opacity: 0;
            }
            .fade-enter-to,
            .fade-leave-from {
                opacity: 1;
            }

            /* 3. Slide Transition (Sidebar) */
            .slide-enter-active,
            .slide-leave-active {
                transition: transform 0.5s ease-in-out;
            }
            .slide-enter-from,
            .slide-leave-to {
                transform: translateX(100%);
            }
            .slide-enter-to,
            .slide-leave-from {
                transform: translateX(0);
            }
        </style>
    @endPushOnce
</x-shop::layouts>
