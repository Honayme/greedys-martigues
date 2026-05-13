@php
    $instagramHandle = 'greedyscreation_';
    $feedPath = storage_path('app/instagram-feed.json');
    $posts = file_exists($feedPath) ? json_decode(file_get_contents($feedPath), true) ?? [] : [];
@endphp

@if(count($posts))

@push('styles')
<style>
    .instagram-section {
        background-color: #F1EADF;
        margin-top: 0;
    }

    .instagram-inner {
        padding: 80px 40px;
        margin: 0 auto;
        max-width: 1400px;
    }

    .instagram-header {
        text-align: center;
        margin-bottom: 48px;
    }

    .instagram-label-row {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 20px;
        margin-bottom: 16px;
    }

    .instagram-label-line {
        width: 60px;
        height: 1px;
        background-color: #060C3B;
        opacity: 0.2;
        flex: none;
    }

    .instagram-section-title {
        font-family: 'DM Serif Display', serif;
        color: #4a4a6b;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.2em;
        white-space: nowrap;
        margin: 0;
        font-weight: 400;
    }

    .instagram-handle {
        font-family: 'DM Serif Display', serif;
        color: #060C3B;
        font-size: 1.75rem;
        font-style: italic;
        margin: 0;
    }

    .instagram-handle a {
        color: inherit;
        text-decoration: none;
        transition: opacity 0.2s ease;
    }

    .instagram-handle a:hover {
        opacity: 0.7;
    }

    .instagram-grid {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        gap: 16px;
        margin-bottom: 40px;
    }

    .instagram-post {
        position: relative;
        overflow: hidden;
        background: #e8e0d0;
        cursor: pointer;
        border-radius: 4px;
        /* Fallback iOS < 15 */
        padding-top: 100%;
        height: 0;
    }

    .instagram-post img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.4s ease;
    }

    @supports (aspect-ratio: 1) {
        .instagram-post {
            aspect-ratio: 1;
            padding-top: 0;
            height: auto;
        }

        .instagram-post img {
            position: static;
        }
    }

    .instagram-post:hover img {
        transform: scale(1.05);
    }

    .instagram-overlay {
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        background: rgba(6, 12, 59, 0.7);
        opacity: 0;
        transition: opacity 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .instagram-post:hover .instagram-overlay {
        opacity: 1;
    }

    .instagram-icon {
        width: 32px;
        height: 32px;
        fill: white;
    }

    .instagram-cta {
        text-align: center;
    }

    .instagram-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 14px 32px;
        border: 1px solid #060C3B;
        background: transparent;
        color: #060C3B;
        font-family: 'Poppins', sans-serif;
        font-size: 14px;
        font-weight: 500;
        text-decoration: none;
        border-radius: 0;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .instagram-btn:hover {
        background: #060C3B;
        color: #fff;
    }

    .instagram-btn svg {
        width: 18px;
        height: 18px;
        transition: transform 0.2s ease;
    }

    .instagram-btn:hover svg {
        transform: translateX(4px);
    }

    @media (max-width: 1200px) {
        .instagram-grid {
            grid-template-columns: repeat(4, 1fr);
        }
    }

    @media (max-width: 768px) {
        .instagram-inner {
            padding: 60px 32px;
        }

        .instagram-grid {
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
        }

        .instagram-handle {
            font-size: 1.5rem;
        }
    }

    @media (max-width: 525px) {
        .instagram-inner {
            padding: 48px 24px;
        }

        .instagram-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 8px;
        }

        .instagram-label-line {
            width: 40px;
        }

        .instagram-section-title {
            font-size: 11px;
        }

        .instagram-handle {
            font-size: 1.25rem;
        }

        .instagram-btn {
            width: 100%;
            justify-content: center;
        }
    }
</style>
@endPush

<section class="instagram-section">
    <div class="instagram-inner">

        <!-- Header -->
        <div class="instagram-header">
            <div class="instagram-label-row">
                <div class="instagram-label-line"></div>
                <h2 class="instagram-section-title">Suivez-nous sur Instagram</h2>
                <div class="instagram-label-line"></div>
            </div>
            <p class="instagram-handle">
                <a href="https://instagram.com/{{ $instagramHandle }}" target="_blank" rel="noopener">
                    @{{ $instagramHandle }}
                </a>
            </p>
        </div>

        <!-- Grid des posts -->
        <div class="instagram-grid">
            @foreach($posts as $post)
                <div class="instagram-post" onclick="window.open('{{ $post['url'] }}', '_blank')">
                    <img src="{{ $post['image'] }}" alt="Publication Instagram {{ $loop->iteration }}">
                    <div class="instagram-overlay">
                        <svg class="instagram-icon" viewBox="0 0 24 24">
                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                        </svg>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- CTA -->
        <div class="instagram-cta">
            <a href="https://instagram.com/{{ $instagramHandle }}" target="_blank" rel="noopener" class="instagram-btn">
                Voir plus sur Instagram
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M5 12h14M12 5l7 7-7 7"/>
                </svg>
            </a>
        </div>

    </div>
</section>

@endif