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

    <v-search></v-search>

    @pushOnce('scripts')
        <script type="text/x-template" id="v-search-template">
            <div class="container px-[60px] max-lg:px-8 max-sm:px-4">
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

                        <!-- Infinite Scroll Trigger & Loading Indicator -->
                        <div v-if="links.next" class="mt-14 flex justify-center">
                            <div v-if="loader" class="flex items-center gap-3">
                                <img
                                    class="h-5 w-5 animate-spin"
                                    src="{{ bagisto_asset('images/spinner.svg') }}"
                                    alt="Loading"
                                />
                                <span class="text-sm text-gray-600">@lang('shop::app.categories.view.load-more')</span>
                            </div>
                            <div ref="loadMoreTrigger" class="h-1"></div>
                        </div>
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
                        loader: false,
                        intersectionObserver: null,
                    }
                },

                computed: {
                    queryParams() {
                        let queryParams = Object.assign({}, this.filters.filter, this.filters.toolbar);

                        // Ajouter le paramètre query depuis l'URL
                        const urlParams = new URLSearchParams(window.location.search);
                        const searchQuery = urlParams.get('query');
                        if (searchQuery) {
                            queryParams.query = searchQuery;
                        }

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
                    links() {
                        this.observeLoadMoreTrigger();
                    },
                },

                mounted() {
                    // Initialiser le filtre avec le paramètre query de l'URL
                    const urlParams = new URLSearchParams(window.location.search);
                    const searchQuery = urlParams.get('query');

                    if (searchQuery) {
                        // Stocker query dans les filtres pour qu'il soit inclus dans queryParams
                        this.filters.toolbar.query = searchQuery;
                    }

                    this.observeLoadMoreTrigger();
                },

                beforeUnmount() {
                    if (this.intersectionObserver) {
                        this.intersectionObserver.disconnect();
                    }
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
                        if (! this.links.next) {
                            return;
                        }

                        this.loader = true;

                        this.$axios.get(this.links.next)
                            .then(response => {
                                this.loader = false;

                                this.products = [...this.products, ...response.data.data];

                                this.links = response.data.links;
                            }).catch(error => {
                                console.log(error);
                                this.loader = false;
                            });
                    },

                    observeLoadMoreTrigger() {
                        this.$nextTick(() => {
                            const trigger = this.$refs.loadMoreTrigger;

                            if (!trigger || !this.links.next) {
                                return;
                            }

                            this.intersectionObserver = new IntersectionObserver(
                                (entries) => {
                                    entries.forEach((entry) => {
                                        if (entry.isIntersecting && this.links.next && !this.loader) {
                                            this.loadMoreProducts();
                                        }
                                    });
                                },
                                { threshold: 0.1 }
                            );

                            this.intersectionObserver.observe(trigger);
                        });
                    },

                    resetObserver() {
                        if (this.intersectionObserver) {
                            this.intersectionObserver.disconnect();
                        }

                        this.observeLoadMoreTrigger();
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
