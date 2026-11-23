@php
    $cartCount = auth()->check() ? (int) auth()->user()->carts()->sum('qty') : 0;
@endphp

<header class="sticky top-0 z-50 border-b border-gray-100 bg-white/95 backdrop-blur">
    <div class="bg-(--text) text-xs text-white">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-2 sm:px-6 lg:px-8">
            <p>🚚 Free delivery in Kathmandu Valley on orders over Rs. 2,000</p>
            <p class="hidden sm:block">Support: +977-9800000000</p>
        </div>
    </div>

    <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3 sm:px-6 lg:px-8">
        <a href="{{ route('home') }}" class="text-xl font-extrabold tracking-tight text-(--primary)">
            {{ config('app.name', 'Nepal Market') }}
        </a>

        <nav class="hidden items-center gap-8 text-sm font-medium md:flex">
            <a href="{{ route('home') }}" class="transition hover:text-(--primary) {{ request()->routeIs('home') ? 'text-(--primary)' : '' }}">Home</a>
            <a href="{{ route('products.index') }}" class="transition hover:text-(--primary) {{ request()->routeIs('products.*') ? 'text-(--primary)' : '' }}">Products</a>
            <a href="{{ route('home') }}#about" class="transition hover:text-(--primary)">About</a>
            <a href="{{ route('merchant.register') }}" class="transition hover:text-(--primary) {{ request()->routeIs('merchant.register') ? 'text-(--primary)' : '' }}">Sell with us</a>
        </nav>

        <div class="flex items-center gap-2 sm:gap-3">
            <a href="{{ route('cart.index') }}" class="relative rounded-full p-2 transition hover:bg-(--primary)/10 hover:text-(--primary)" aria-label="Cart, {{ $cartCount }} items">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.4l.4 2m0 0 1.6 8.2a1 1 0 0 0 1 .8h9.5a1 1 0 0 0 1-.8L20 6H4.05ZM9 19.5a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm9 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z"/></svg>
                @if ($cartCount > 0)
                    <span class="absolute -right-0.5 -top-0.5 flex h-4 min-w-4 items-center justify-center rounded-full bg-(--primary) px-1 text-[10px] font-bold text-white">{{ $cartCount > 99 ? '99+' : $cartCount }}</span>
                @endif
            </a>

            @auth
                <span class="hidden max-w-32 truncate text-sm font-semibold lg:block">{{ auth()->user()->name }}</span>
                <a href="{{ route('orders.index') }}" class="hidden rounded-lg px-3 py-2 text-sm font-semibold transition hover:text-(--primary) sm:block">Orders</a>
                <form method="POST" action="{{ route('logout') }}" class="hidden sm:block">
                    @csrf
                    <button type="submit" class="rounded-lg px-4 py-2 text-sm font-semibold transition hover:text-(--primary)">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="hidden rounded-lg px-4 py-2 text-sm font-semibold transition hover:text-(--primary) sm:block">Login</a>
            @endauth

            <a href="{{ route('merchant.register') }}" class="hidden rounded-lg bg-(--primary) px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:opacity-90 sm:block">Merchant Register</a>

            <button id="menu-btn" type="button" class="rounded-lg p-2 hover:bg-gray-100 md:hidden" aria-label="Toggle menu" aria-expanded="false" aria-controls="mobile-menu">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
        </div>
    </div>

    <div id="mobile-menu" class="hidden border-t border-gray-100 bg-white md:hidden">
        <nav class="mx-auto flex max-w-7xl flex-col gap-1 px-4 py-3 text-sm font-medium">
            <a href="{{ route('home') }}" class="rounded-lg px-3 py-2 hover:bg-(--primary)/10 hover:text-(--primary)">Home</a>
            <a href="{{ route('products.index') }}" class="rounded-lg px-3 py-2 hover:bg-(--primary)/10 hover:text-(--primary)">Products</a>
            <a href="{{ route('home') }}#about" class="rounded-lg px-3 py-2 hover:bg-(--primary)/10 hover:text-(--primary)">About</a>
            <a href="{{ route('merchant.register') }}" class="rounded-lg px-3 py-2 hover:bg-(--primary)/10 hover:text-(--primary)">Sell with us</a>
            @auth
                <a href="{{ route('orders.index') }}" class="rounded-lg px-3 py-2 hover:bg-(--primary)/10 hover:text-(--primary)">Order history</a>
            @endauth
            <div class="mt-2 grid grid-cols-2 gap-2">
                @auth
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-center font-semibold">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="rounded-lg border border-gray-200 px-3 py-2 text-center font-semibold">Login</a>
                @endauth
                <a href="{{ route('merchant.register') }}" class="rounded-lg bg-(--primary) px-3 py-2 text-center font-semibold text-white">Merchant Register</a>
            </div>
        </nav>
    </div>

    <script>
        const menuButton = document.getElementById('menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');

        menuButton?.addEventListener('click', () => {
            const isExpanded = menuButton.getAttribute('aria-expanded') === 'true';
            menuButton.setAttribute('aria-expanded', String(!isExpanded));
            mobileMenu?.classList.toggle('hidden');
        });
    </script>
</header>
