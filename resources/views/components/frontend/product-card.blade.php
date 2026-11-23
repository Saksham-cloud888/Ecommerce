@props(['product'])

@php
    $variant = $product->cheapest_variant;
    $variantCount = $product->product_varients->count();
    $hasDiscount = $variant && $variant->discount > 0;
@endphp

<article class="group flex flex-col overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-xl">
    <a href="{{ route('products.show', $product->slug) }}" class="relative block aspect-[4/3] overflow-hidden bg-gradient-to-br from-(--primary)/10 to-(--secondary)/10">
        @if ($product->image_url)
            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" loading="lazy" class="h-full w-full object-cover transition duration-300 group-hover:scale-105">
        @else
            <span class="flex h-full w-full items-center justify-center text-7xl font-extrabold text-(--primary)/40" aria-hidden="true">{{ mb_strtoupper(mb_substr($product->name, 0, 1)) }}</span>
        @endif

        @if ($hasDiscount)
            <span class="absolute left-3 top-3 rounded-full bg-(--primary) px-3 py-1 text-xs font-bold text-white">-{{ $variant->discount_percent }}%</span>
        @endif
    </a>

    <div class="flex flex-1 flex-col p-5">
        <div class="flex items-center justify-between gap-2 text-xs">
            <a href="{{ route('products.index', ['merchant' => $product->merchant_id]) }}" class="truncate font-semibold uppercase tracking-wide text-(--secondary) hover:underline">
                {{ $product->merchant?->shop_name }}
            </a>
            @if ($variantCount > 1)
                <span class="shrink-0 rounded-full bg-gray-100 px-2 py-0.5 font-medium text-(--text)/70">{{ $variantCount }} options</span>
            @endif
        </div>

        <h3 class="mt-2 text-lg font-bold leading-snug">
            <a href="{{ route('products.show', $product->slug) }}" class="transition hover:text-(--primary)">{{ $product->name }}</a>
        </h3>

        @if ($product->description)
            <p class="mt-1 line-clamp-2 text-sm text-(--text)/60">{{ $product->description }}</p>
        @endif

        <div class="mt-auto flex items-end justify-between gap-2 pt-4">
            <div>
                @if ($variantCount > 1)
                    <span class="block text-xs text-(--text)/60">Starting from</span>
                @endif
                <span class="text-xl font-extrabold text-(--primary)">Rs. {{ number_format($variant->final_price) }}</span>
                @if ($hasDiscount)
                    <span class="ml-1 text-sm text-gray-400 line-through">Rs. {{ number_format($variant->price) }}</span>
                @endif
            </div>
            <a href="{{ route('products.show', $product->slug) }}" class="rounded-lg bg-(--text) px-4 py-2 text-sm font-semibold text-white transition hover:bg-(--primary)">View</a>
        </div>
    </div>
</article>
