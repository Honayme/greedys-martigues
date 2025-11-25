<div class="flex flex-col gap-4">
    <!-- Google Button - En Avant -->
    @if (core()->getConfigData('customer.settings.social_login.enable_google'))
        <a
            href="{{ route('customer.social-login.index', 'google') }}"
            class="flex items-center justify-center gap-3 w-full rounded-lg border-2 border-gray-300 bg-white px-4 py-3 text-base font-semibold text-gray-700 transition-all hover:bg-gray-50 hover:border-gray-400 active:bg-gray-100"
            aria-label="Google"
        >
            <div class="h-5 w-5">
                @include('social_login::icons.google')
            </div>
            <span>Continuer avec Google</span>
        </a>
    @endif

    <!-- Autres Réseaux Sociaux -->
    @php
        $otherSocials = ['enable_facebook', 'enable_twitter', 'enable_linkedin-openid', 'enable_github'];
    @endphp

    @if (collect($otherSocials)->filter(fn($social) => core()->getConfigData('customer.settings.social_login.' . $social))->count() > 0)
        <div class="flex gap-3 justify-center">
            @foreach($otherSocials as $social)
                @if (! core()->getConfigData('customer.settings.social_login.' . $social))
                    @continue
                @endif

                @php
                    $icon = explode('_', $social);
                @endphp

                <a
                    href="{{ route('customer.social-login.index', $icon[1]) }}"
                    class="flex items-center justify-center h-12 w-12 rounded-lg border border-gray-300 bg-white transition-all hover:bg-gray-50 hover:border-gray-400 active:bg-gray-100"
                    aria-label="{{ $icon[0] }}"
                    title="{{ ucfirst($icon[0]) }}"
                >
                    <div class="h-5 w-5">
                        @include('social_login::icons.' . $icon[1])
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>
