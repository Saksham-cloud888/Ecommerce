<x-frontend.frontendlayout :title="'My Cart | ' . config('app.name', 'Ecommerce')">
    <section class="bg-gradient-to-br from-(--primary)/10 via-white to-(--secondary)/10">
        <div class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-extrabold sm:text-4xl">My Cart</h1>
            <p class="mt-2 text-(--text)/70">Items are grouped by shop. Each shop's items are ordered, paid and delivered separately.</p>
        </div>
    </section>

    <section class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8">
        @if (session('success'))
            <p role="status" class="mb-6 rounded-xl border border-(--secondary)/30 bg-(--secondary)/10 p-4 text-sm font-medium text-(--secondary)">{{ session('success') }}</p>
        @endif
        @if (session('error') || $errors->any())
            <p role="alert" class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm font-medium text-red-700">{{ session('error') ?? $errors->first() }}</p>
        @endif

        @if ($groups->isEmpty() && $unavailable->isEmpty())
            <div class="rounded-2xl border border-dashed border-gray-200 p-14 text-center">
                <p class="text-5xl" aria-hidden="true">🛒</p>
                <h2 class="mt-4 text-xl font-bold">Your cart is empty</h2>
                <p class="mt-1 text-sm text-(--text)/60">Find something you like and add it to your cart.</p>
                <a href="{{ route('products.index') }}" class="mt-6 inline-block rounded-xl bg-(--primary) px-7 py-3 font-semibold text-white shadow-lg shadow-(--primary)/30 transition hover:opacity-95">Browse products</a>
            </div>
        @endif

        <div class="space-y-8">
            @foreach ($groups as $merchantId => $group)
                <article class="overflow-hidden rounded-3xl border border-gray-100 bg-white shadow-sm">
                    <header class="flex items-center justify-between gap-4 border-b border-gray-100 bg-gray-50 px-5 py-4 sm:px-6">
                        <div class="flex items-center gap-3">
                            <span class="flex h-10 w-10 items-center justify-center rounded-full bg-(--secondary) font-bold text-white">{{ mb_strtoupper(mb_substr($group['merchant']->shop_name, 0, 1)) }}</span>
                            <div>
                                <h2 class="font-bold leading-tight">{{ $group['merchant']->shop_name }}</h2>
                                <p class="text-xs text-(--text)/60">{{ $group['quantity'] }} {{ Str::plural('item', $group['quantity']) }}</p>
                            </div>
                        </div>
                        <a href="{{ route('products.index', ['merchant' => $merchantId]) }}" class="text-sm font-semibold text-(--secondary) hover:underline">Shop more →</a>
                    </header>

                    <ul class="divide-y divide-gray-100">
                        @foreach ($group['items'] as $line)
                            @php
                                $variant = $line->product_varient;
                                $product = $variant->product;
                                $image = $variant->image_url ?: $product->image_url;
                                $hasDiscount = $variant->discount > 0;
                            @endphp

                            <li class="flex flex-col gap-4 p-5 sm:flex-row sm:items-center sm:p-6">
                                <a href="{{ route('products.show', $product->slug) }}" class="block h-24 w-24 shrink-0 overflow-hidden rounded-2xl bg-gradient-to-br from-(--primary)/10 to-(--secondary)/10">
                                    @if ($image)
                                        <img src="{{ $image }}" alt="{{ $product->name }}" loading="lazy" class="h-full w-full object-cover">
                                    @else
                                        <span class="flex h-full w-full items-center justify-center text-4xl font-extrabold text-(--primary)/40" aria-hidden="true">{{ mb_strtoupper(mb_substr($product->name, 0, 1)) }}</span>
                                    @endif
                                </a>

                                <div class="min-w-0 flex-1">
                                    <a href="{{ route('products.show', $product->slug) }}" class="font-bold leading-snug transition hover:text-(--primary)">{{ $product->name }}</a>
                                    <p class="mt-0.5 text-sm text-(--text)/60">Option: {{ $variant->name }}</p>
                                    <p class="mt-1 text-sm">
                                        <span class="font-semibold text-(--primary)">Rs. {{ number_format($variant->final_price) }}</span>
                                        @if ($hasDiscount)
                                            <span class="ml-1 text-gray-400 line-through">Rs. {{ number_format($variant->price) }}</span>
                                        @endif
                                    </p>
                                </div>

                                <div class="flex items-center justify-between gap-4 sm:justify-end">
                                    <form action="{{ route('cart.update', $line) }}" method="POST" class="inline-flex items-center overflow-hidden rounded-xl border border-gray-200">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" name="qty" value="{{ $line->qty - 1 }}" @disabled($line->qty <= 1) class="px-3.5 py-2 text-lg font-bold transition hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-30" aria-label="Decrease quantity">−</button>
                                        <span class="w-10 text-center text-sm font-semibold" aria-live="polite">{{ $line->qty }}</span>
                                        <button type="submit" name="qty" value="{{ $line->qty + 1 }}" @disabled($line->qty >= \App\Models\Cart::MAX_QTY) class="px-3.5 py-2 text-lg font-bold transition hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-30" aria-label="Increase quantity">+</button>
                                    </form>

                                    <p class="w-28 text-right font-bold">Rs. {{ number_format($line->line_total) }}</p>

                                    <form action="{{ route('cart.destroy', $line) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-lg p-2 text-gray-400 transition hover:bg-red-50 hover:text-red-600" aria-label="Remove {{ $product->name }}">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 7h12m-9 0V5a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2m2 0v12a1 1 0 0 1-1 1H8a1 1 0 0 1-1-1V7h10Z"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </li>
                        @endforeach
                    </ul>

                    <footer class="flex flex-col gap-4 border-t border-gray-100 bg-gray-50 px-5 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                        <div>
                            <p class="text-sm text-(--text)/60">Subtotal from {{ $group['merchant']->shop_name }}</p>
                            <p class="text-2xl font-extrabold text-(--primary)">Rs. {{ number_format($group['subtotal']) }}</p>
                        </div>
                        <a href="{{ route('checkout.show', $merchantId) }}" class="rounded-xl bg-(--primary) px-8 py-3.5 text-center font-bold text-white shadow-lg shadow-(--primary)/30 transition hover:opacity-95">Checkout this shop →</a>
                    </footer>
                </article>
            @endforeach
        </div>

        @if ($unavailable->isNotEmpty())
            <div class="mt-8 rounded-2xl border border-amber-200 bg-amber-50 p-5">
                <h2 class="font-bold text-amber-900">No longer available</h2>
                <p class="mt-1 text-sm text-amber-800">These items can't be ordered right now. Remove them to keep your cart tidy.</p>
                <ul class="mt-4 space-y-2">
                    @foreach ($unavailable as $line)
                        <li class="flex items-center justify-between gap-3 rounded-xl bg-white px-4 py-3 text-sm">
                            <span class="truncate">
                                {{ $line->product_varient?->product?->name ?? 'Removed item' }}
                                @if ($line->product_varient)
                                    <span class="text-(--text)/60">· {{ $line->product_varient->name }}</span>
                                @endif
                            </span>
                            <form action="{{ route('cart.destroy', $line) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="font-semibold text-red-600 hover:underline">Remove</button>
                            </form>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </section>
</x-frontend.frontendlayout>
