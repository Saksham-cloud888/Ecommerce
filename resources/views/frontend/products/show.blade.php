@php
    $variants = $product->product_varients->sortBy('id')->values();
    $default = $product->cheapest_variant;

    $variantData = $variants->map(fn ($variant) => [
        'id' => $variant->id,
        'name' => $variant->name,
        'price' => (float) $variant->price,
        'final' => $variant->final_price,
        'discount' => (float) $variant->discount,
        'percent' => $variant->discount_percent,
        'image' => $variant->image_url,
    ])->values();

    $startImage = $default->image_url ?: $product->image_url;
    $hasDiscount = $default->discount > 0;
@endphp

<x-frontend.frontendlayout :title="$product->name . ' | ' . config('app.name', 'Ecommerce')">
    @if (session('success') || session('error') || $errors->any())
        <div class="mx-auto max-w-7xl px-4 pt-6 sm:px-6 lg:px-8">
            @if (session('success'))
                <p role="status" class="flex flex-wrap items-center justify-between gap-2 rounded-xl border border-(--secondary)/30 bg-(--secondary)/10 p-4 text-sm font-medium text-(--secondary)">
                    <span>{{ session('success') }}</span>
                    <a href="{{ route('cart.index') }}" class="font-bold underline">View cart →</a>
                </p>
            @else
                <p role="alert" class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm font-medium text-red-700">{{ session('error') ?? $errors->first() }}</p>
            @endif
        </div>
    @endif

    <form id="cart-form" action="{{ route('cart.store') }}" method="POST" class="hidden">@csrf</form>

    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <nav class="text-sm text-(--text)/60" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="hover:text-(--primary)">Home</a>
            <span class="mx-1">/</span>
            <a href="{{ route('products.index') }}" class="hover:text-(--primary)">Products</a>
            <span class="mx-1">/</span>
            <span class="font-medium text-(--text)">{{ $product->name }}</span>
        </nav>
    </div>

    <section class="mx-auto grid max-w-7xl gap-10 px-4 pb-16 sm:px-6 lg:grid-cols-2 lg:gap-14 lg:px-8">
        <div>
            <div class="relative aspect-square overflow-hidden rounded-3xl border border-gray-100 bg-gradient-to-br from-(--primary)/10 to-(--secondary)/10 shadow-sm">
                <img id="product-image" src="{{ $startImage ?? '' }}" alt="{{ $product->name }}" class="h-full w-full object-cover {{ $startImage ? '' : 'hidden' }}">
                <span id="product-placeholder" class="flex h-full w-full items-center justify-center text-9xl font-extrabold text-(--primary)/30 {{ $startImage ? 'hidden' : '' }}" aria-hidden="true">
                    {{ mb_strtoupper(mb_substr($product->name, 0, 1)) }}
                </span>
                <span id="badge-percent" class="absolute left-4 top-4 rounded-full bg-(--primary) px-3 py-1 text-sm font-bold text-white {{ $hasDiscount ? '' : 'hidden' }}">-{{ $default->discount_percent }}%</span>
            </div>
        </div>

        <div>
            <a href="{{ route('products.index', ['merchant' => $product->merchant_id]) }}" class="text-sm font-semibold uppercase tracking-wide text-(--secondary) hover:underline">
                {{ $product->merchant?->shop_name }}
            </a>
            <h1 class="mt-2 text-3xl font-extrabold leading-tight sm:text-4xl">{{ $product->name }}</h1>

            <div class="mt-5 flex flex-wrap items-baseline gap-x-3 gap-y-1">
                <span id="price-final" class="text-4xl font-extrabold text-(--primary)">Rs. {{ number_format($default->final_price) }}</span>
                <span id="price-old" class="text-lg text-gray-400 line-through {{ $hasDiscount ? '' : 'hidden' }}">Rs. {{ number_format($default->price) }}</span>
                <span id="price-save" class="rounded-full bg-(--secondary)/10 px-3 py-1 text-sm font-semibold text-(--secondary) {{ $hasDiscount ? '' : 'hidden' }}">You save Rs. {{ number_format($default->discount) }}</span>
            </div>

            @if ($product->description)
                <p class="mt-5 whitespace-pre-line leading-relaxed text-(--text)/80">{{ $product->description }}</p>
            @endif

            <fieldset class="mt-7">
                <legend class="text-sm font-semibold">Choose an option</legend>
                <div class="mt-3 flex flex-wrap gap-3">
                    @foreach ($variants as $variant)
                        <label class="cursor-pointer">
                            <input type="radio" name="variant_id" form="cart-form" value="{{ $variant->id }}" class="peer sr-only" @checked($variant->id === $default->id)>
                            <span class="block rounded-xl border-2 border-gray-200 px-4 py-2.5 text-sm font-semibold transition hover:border-gray-300 peer-checked:border-(--primary) peer-checked:bg-(--primary)/10 peer-checked:text-(--primary) peer-focus-visible:ring-4 peer-focus-visible:ring-(--primary)/20">{{ $variant->name }}</span>
                        </label>
                    @endforeach
                </div>
            </fieldset>

            <div class="mt-7 flex flex-wrap items-center gap-4">
                <div class="inline-flex items-center overflow-hidden rounded-xl border border-gray-200">
                    <button type="button" data-qty-step="-1" class="px-4 py-3 text-lg font-bold transition hover:bg-gray-50" aria-label="Decrease quantity">−</button>
                    <input id="qty" name="qty" form="cart-form" type="number" min="1" max="10" value="1" class="w-14 [appearance:textfield] border-x border-gray-200 py-3 text-center text-sm font-semibold outline-none [&amp;::-webkit-inner-spin-button]:appearance-none [&amp;::-webkit-outer-spin-button]:appearance-none" aria-label="Quantity">
                    <button type="button" data-qty-step="1" class="px-4 py-3 text-lg font-bold transition hover:bg-gray-50" aria-label="Increase quantity">+</button>
                </div>
                <button id="add-to-cart" type="submit" form="cart-form" class="flex-1 rounded-xl bg-(--primary) px-8 py-3.5 font-bold text-white shadow-lg shadow-(--primary)/30 transition hover:opacity-95 sm:flex-none">Add to Cart</button>
            </div>

            <ul class="mt-8 grid gap-3 border-t border-gray-100 pt-6 text-sm sm:grid-cols-3">
                @foreach ([['🔒', 'Secure payment with Khalti'], ['✅', 'Verified merchant'], ['↩️', 'Easy returns']] as [$icon, $text])
                    <li class="flex items-center gap-2 text-(--text)/80">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-(--primary)/10">{{ $icon }}</span>
                        {{ $text }}
                    </li>
                @endforeach
            </ul>

            <div class="mt-6 flex items-center justify-between gap-4 rounded-2xl bg-gray-50 p-4">
                <div class="flex items-center gap-3">
                    <span class="flex h-11 w-11 items-center justify-center rounded-full bg-(--secondary) font-bold text-white">{{ mb_strtoupper(mb_substr($product->merchant?->shop_name ?? '?', 0, 1)) }}</span>
                    <div>
                        <p class="text-xs text-(--text)/60">Sold by</p>
                        <p class="font-semibold">{{ $product->merchant?->shop_name }}</p>
                    </div>
                </div>
                <a href="{{ route('products.index', ['merchant' => $product->merchant_id]) }}" class="text-sm font-semibold text-(--secondary) hover:underline">View shop →</a>
            </div>
        </div>
    </section>

    @if ($related->isNotEmpty())
        <section class="border-t border-gray-100 bg-gray-50">
            <div class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
                <h2 class="text-2xl font-extrabold sm:text-3xl">You may also like</h2>
                <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($related as $item)
                        <x-frontend.product-card :product="$item" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <script>
        (() => {
            const variants = @json($variantData);
            const productImage = @json($product->image_url);
            const elements = {
                final: document.getElementById('price-final'),
                old: document.getElementById('price-old'),
                save: document.getElementById('price-save'),
                badge: document.getElementById('badge-percent'),
                image: document.getElementById('product-image'),
                placeholder: document.getElementById('product-placeholder'),
                quantity: document.getElementById('qty'),
            };
            const money = (amount) => 'Rs. ' + Math.round(amount).toLocaleString('en-US');

            function renderVariant(variant) {
                const hasDiscount = variant.discount > 0;
                elements.final.textContent = money(variant.final);
                elements.old.textContent = money(variant.price);
                elements.save.textContent = 'You save ' + money(variant.discount);
                elements.badge.textContent = '-' + variant.percent + '%';
                [elements.old, elements.save, elements.badge].forEach((element) => element.classList.toggle('hidden', !hasDiscount));

                const imageUrl = variant.image || productImage;
                if (imageUrl) elements.image.src = imageUrl;
                elements.image.classList.toggle('hidden', !imageUrl);
                elements.placeholder.classList.toggle('hidden', Boolean(imageUrl));
            }

            document.querySelectorAll('input[name="variant_id"]').forEach((radio) => {
                radio.addEventListener('change', () => {
                    const selected = variants.find((variant) => String(variant.id) === radio.value);
                    if (selected) renderVariant(selected);
                });
            });

            document.querySelectorAll('[data-qty-step]').forEach((button) => {
                button.addEventListener('click', () => {
                    const next = (parseInt(elements.quantity.value, 10) || 1) + parseInt(button.dataset.qtyStep, 10);
                    elements.quantity.value = Math.min(10, Math.max(1, next));
                });
            });
        })();
    </script>
</x-frontend.frontendlayout>
