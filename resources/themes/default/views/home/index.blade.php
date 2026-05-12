@php
    $channel = core()->getCurrentChannel();
@endphp

<!-- SEO Meta Content -->
@push('meta')
    <meta name="title" content="Greedy's Création — Bijoux artisanaux faits main à Martigues" />
    <meta name="description" content="Découvrez les créations uniques de MO, créatrice artisanale à Martigues. Bijoux faits main en acier inoxydable, argent et pierres naturelles." />
    <meta name="keywords" content="{{ $channel->home_seo['meta_keywords'] ?? '' }}" />
@endpush

<x-shop::layouts>
    <x-slot:title>
        Greedy's Création — Bijoux artisanaux faits main à Martigues
    </x-slot>

    <!-- Customizations Bagisto (carrousels, contenu statique, catégories, produits) -->
    @foreach ($customizations as $customization)
        @php ($data = $customization->options) @endphp

        @switch ($customization->type)
            @case ($customization::IMAGE_CAROUSEL)
                <x-shop::carousel :options="$data" aria-label="Image Carousel" />
                @break

            @case ($customization::STATIC_CONTENT)
                @if (! empty($data['css']))
                    @push('styles')
                        <style>{{ $data['css'] }}</style>
                    @endpush
                @endif

                @if (! empty($data['html']))
                    {!! $data['html'] !!}
                @endif
                @break

            @case ($customization::CATEGORY_CAROUSEL)
                <x-shop::categories.carousel
                    :title="$data['title'] ?? ''"
                    :src="route('shop.api.categories.index', $data['filters'] ?? [])"
                    :navigation-link="route('shop.home.index')"
                    aria-label="Categories Carousel"
                />
                @break

            @case ($customization::PRODUCT_CAROUSEL)
                <x-shop::products.carousel
                    :title="$data['title'] ?? ''"
                    :src="route('shop.api.products.index', $data['filters'] ?? [])"
                    :navigation-link="route('shop.search.index', $data['filters'] ?? [])"
                    aria-label="Product Carousel"
                />
                @break
        @endswitch
    @endforeach

    @push('styles')
    <style>
        .creatrice-section {
            background-color: #F1EADF;
            margin-top: 64px;
        }
        .creatrice-inner {
            padding: 80px 40px;
            margin: 0 auto;
            text-align: left;
        }
        .creatrice-wrapper {
            max-width: 900px;
            margin: 0 auto;
            text-align: center;
        }
        .creatrice-label-row {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 20px;
            margin-bottom: 40px;
        }
        .creatrice-label-line {
            width: 60px;
            height: 1px;
            background-color: #060C3B;
            opacity: 0.2;
            flex: none;
        }
        .creatrice-h1 {
            color: #4a4a6b;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.18em;
            white-space: nowrap;
            margin: 0;
        }
        .creatrice-accroche {
            color: #060C3B;
            font-size: 2.75rem;
            font-style: italic;
            line-height: 1.2;
            margin: 0 auto 40px;
            max-width: 24ch;
        }
        .creatrice-body {
            color: #1a1a1a;
            font-size: 1.125rem;
            line-height: 1.75;
            max-width: 75ch;
            margin: 0 auto;
        }
        .creatrice-signature {
            color: #6b7280;
            font-size: 0.875rem;
            font-style: italic;
            margin-top: 40px;
        }
        .creatrice-divider {
            width: 100%;
            height: 1px;
            background-color: #060C3B;
            opacity: 0.1;
        }
        @media (max-width: 768px) {
            .creatrice-section { margin-top: 32px; }
            .creatrice-inner { padding: 60px 32px; }
            .creatrice-accroche { font-size: 2.25rem; margin-bottom: 32px; }
            .creatrice-body { font-size: 1rem; }
        }
        @media (max-width: 525px) {
            .creatrice-inner { padding: 48px 24px; }
            .creatrice-accroche { font-size: 1.875rem; }
        }
    </style>
    @endpush

    <!-- Section Créatrice -->
    <section class="creatrice-section">

        <div class="creatrice-inner">

            <div class="creatrice-wrapper">

                <div class="creatrice-label-row">
                    <div class="creatrice-label-line"></div>
                    <h1 class="creatrice-h1">Bijoux artisanaux — Greedy's Création, Martigues</h1>
                    <div class="creatrice-label-line"></div>
                </div>

                <p class="creatrice-accroche">
                    Créations instinctives,<br>
                    uniques, énergiques,<br>
                    colorées et singulières.
                </p>

                <p class="creatrice-body">
                    Autodidacte depuis 1995, curieuse des techniques, ayant un esprit plus que créatif,
                    je me suis intéressée à plusieurs matières, argile polymère, résine,
                    pigments, pierres semi-précieuses. Mes idées partent souvent d'un assemblage
                    de couleurs, puis vient l'idée de la forme... mais à tout moment, cela peut changer.
                </p>

                <p class="creatrice-signature">— MO, créatrice depuis 1995</p>

            </div>

        </div>

        <div class="creatrice-divider"></div>
    </section>

    <!-- Section Témoignages -->
    @includeIf('shop::home.testimonials-section')

    <!-- Section Instagram -->
    @includeIf('shop::home.instagram-section')

</x-shop::layouts>
