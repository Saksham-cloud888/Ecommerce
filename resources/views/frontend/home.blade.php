<x-frontend.frontendlayout :title="'Home | ' . config('app.name', 'Ecommerce')">
    @if (session('success'))
        <div role="status" class="mx-auto mt-6 max-w-7xl px-4 sm:px-6 lg:px-8">
            <p class="rounded-xl border border-(--secondary)/30 bg-(--secondary)/10 p-4 text-sm font-medium text-(--secondary)">{{ session('success') }}</p>
        </div>
    @endif

    <section class="relative overflow-hidden bg-gradient-to-br from-(--primary)/10 via-white to-(--secondary)/10">
        <div class="mx-auto grid max-w-7xl items-center gap-12 px-4 py-16 sm:px-6 lg:grid-cols-2 lg:px-8 lg:py-24">
            <div>
                <span class="inline-block rounded-full bg-(--secondary)/10 px-4 py-1 text-sm font-semibold text-(--secondary)">
                    🇳🇵 Nepal's multi-vendor marketplace
                </span>
                <h1 class="mt-5 text-4xl font-extrabold leading-tight tracking-tight sm:text-5xl lg:text-6xl">
                    Shop from <span class="text-(--primary)">trusted local</span> merchants
                </h1>
                <p class="mt-5 max-w-xl text-lg leading-relaxed text-(--text)/80">
                    Discover quality products from verified sellers, pay securely with Khalti, and get them delivered to your door.
                </p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="#products" class="rounded-xl bg-(--primary) px-7 py-3.5 font-semibold text-white shadow-lg shadow-(--primary)/30 transition hover:-translate-y-0.5 hover:opacity-95">Shop Now</a>
                    <a href="{{ route('merchant.register') }}" class="rounded-xl border-2 border-(--secondary) px-7 py-3.5 font-semibold text-(--secondary) transition hover:bg-(--secondary) hover:text-white">Become a Merchant</a>
                </div>
            </div>

            <div class="relative mx-auto grid w-full max-w-md grid-cols-2 gap-4" aria-hidden="true">
                <div class="flex aspect-square items-center justify-center rounded-3xl bg-(--primary)/15 text-7xl shadow-sm">🧣</div>
                <div class="mt-8 flex aspect-square items-center justify-center rounded-3xl bg-(--secondary)/15 text-7xl shadow-sm">🍵</div>
                <div class="-mt-4 flex aspect-square items-center justify-center rounded-3xl bg-(--secondary)/15 text-7xl shadow-sm">🎧</div>
                <div class="flex aspect-square items-center justify-center rounded-3xl bg-(--primary)/15 text-7xl shadow-sm">🎒</div>
            </div>
        </div>
    </section>

    <section class="border-y border-gray-100 bg-white">
        <div class="mx-auto grid max-w-7xl gap-6 px-4 py-8 sm:grid-cols-2 sm:px-6 lg:grid-cols-4 lg:px-8">
            @foreach ([['🚚', 'Fast Delivery', 'Quick shipping across Nepal'], ['🔒', 'Secure Payment', 'Pay safely with Khalti'], ['✅', 'Verified Merchants', 'Every seller is approved'], ['↩️', 'Easy Returns', 'Hassle-free return policy']] as [$icon, $title, $desc])
                <div class="flex items-center gap-4">
                    <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-(--primary)/10 text-2xl">{{ $icon }}</span>
                    <div>
                        <h2 class="font-semibold">{{ $title }}</h2>
                        <p class="text-sm text-(--text)/70">{{ $desc }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <section id="products" class="mx-auto max-w-7xl scroll-mt-28 px-4 py-16 sm:px-6 lg:px-8 lg:py-20">
        <div class="mx-auto max-w-2xl text-center">
            <p class="text-sm font-semibold uppercase tracking-wider text-(--primary)">Featured</p>
            <h2 class="mt-2 text-3xl font-extrabold sm:text-4xl">Popular Products</h2>
            <p class="mt-3 text-(--text)/70">Fresh arrivals from our approved merchants.</p>
        </div>

        @if ($products->isEmpty())
            <p class="mt-12 rounded-2xl border border-dashed border-gray-200 p-10 text-center text-(--text)/60">No products yet. Check back soon!</p>
        @else
            <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($products as $product)
                    <x-frontend.product-card :product="$product" />
                @endforeach
            </div>

            <div class="mt-10 text-center">
                <a href="{{ route('products.index') }}" class="inline-block rounded-xl border-2 border-(--primary) px-7 py-3 font-semibold text-(--primary) transition hover:bg-(--primary) hover:text-white">View all products →</a>
            </div>
        @endif
    </section>

    <section id="about" class="scroll-mt-28 bg-gray-50">
        <div class="mx-auto grid max-w-7xl items-center gap-12 px-4 py-16 sm:px-6 lg:grid-cols-2 lg:px-8 lg:py-20">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wider text-(--primary)">About us</p>
                <h2 class="mt-2 text-3xl font-extrabold sm:text-4xl">One marketplace, many local sellers</h2>
                <p class="mt-4 leading-relaxed text-(--text)/80">
                    {{ config('app.name', 'We') }} brings together independent merchants from across Nepal on a single, easy-to-use platform. Every merchant is reviewed and approved by our team, so you can shop with confidence while supporting local businesses.
                </p>
                <ul class="mt-6 space-y-3">
                    @foreach (['Every merchant manually verified', 'Secure online payments via Khalti', 'Track all your orders in one place'] as $point)
                        <li class="flex items-start gap-3">
                            <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-(--secondary) text-xs text-white">✓</span>
                            <span>{{ $point }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="grid grid-cols-2 gap-4">
                @foreach ([[number_format($stats['merchants']), 'Verified Merchants'], [number_format($stats['products']), 'Products Listed'], [number_format($stats['variants']), 'Options to Choose'], ['Khalti', 'Secure Payments']] as [$num, $label])
                    <div class="rounded-2xl bg-white p-6 text-center shadow-sm">
                        <p class="text-3xl font-extrabold text-(--primary) sm:text-4xl">{{ $num }}</p>
                        <p class="mt-1 text-sm text-(--text)/70">{{ $label }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8 lg:py-20">
        <div class="overflow-hidden rounded-3xl bg-gradient-to-br from-(--primary) to-(--secondary) p-8 text-white shadow-xl sm:p-12 lg:p-16">
            <div class="grid items-center gap-10 lg:grid-cols-2">
                <div>
                    <h2 class="text-3xl font-extrabold sm:text-4xl">Own a shop? Start selling with us.</h2>
                    <p class="mt-4 text-white/90">Register your shop, get approved by our admin team, and reach thousands of customers. It only takes a few minutes to apply.</p>
                    <a href="{{ route('merchant.register') }}" class="mt-8 inline-block rounded-xl bg-white px-8 py-3.5 font-bold text-(--primary) shadow-lg transition hover:-translate-y-0.5">Register Your Shop →</a>
                </div>
                <ol class="grid gap-4 sm:grid-cols-2">
                    @foreach ([['Submit request', 'Fill in your shop details'], ['Admin review', 'We verify your information'], ['Get approved', 'Receive an approval email'], ['Start selling', 'Log in and add products']] as $i => [$title, $desc])
                        <li class="rounded-2xl bg-white/15 p-5 backdrop-blur">
                            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-white font-bold text-(--primary)">{{ $i + 1 }}</span>
                            <h3 class="mt-3 font-bold">{{ $title }}</h3>
                            <p class="text-sm text-white/85">{{ $desc }}</p>
                        </li>
                    @endforeach
                </ol>
            </div>
        </div>
    </section>
</x-frontend.frontendlayout>
