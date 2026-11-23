<x-frontend.frontendlayout :title="'All Products | ' . config('app.name', 'Ecommerce')">
    <section class="bg-gradient-to-br from-(--primary)/10 via-white to-(--secondary)/10">
        <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <nav class="text-sm text-(--text)/60" aria-label="Breadcrumb">
                <a href="{{ route('home') }}" class="hover:text-(--primary)">Home</a>
                <span class="mx-1">/</span>
                <span class="font-medium text-(--text)">Products</span>
            </nav>
            <h1 class="mt-3 text-3xl font-extrabold sm:text-4xl">All Products</h1>
            <p class="mt-2 text-(--text)/70">Browse items from verified merchants across Nepal.</p>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        @php
            $field = 'w-full rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm outline-none transition focus:border-(--primary) focus:ring-4 focus:ring-(--primary)/15';
        @endphp
        <form method="GET" action="{{ route('products.index') }}" class="grid gap-3 rounded-2xl border border-gray-100 bg-white p-4 shadow-sm sm:grid-cols-2 lg:grid-cols-[1fr_14rem_14rem_auto]">
            <div>
                <label for="q" class="sr-only">Search</label>
                <input id="q" name="q" type="search" value="{{ $search }}" maxlength="100" placeholder="Search products or shops…" class="{{ $field }}">
            </div>

            <div>
                <label for="merchant" class="sr-only">Shop</label>
                <select id="merchant" name="merchant" class="{{ $field }}">
                    <option value="">All shops</option>
                    @foreach ($merchants as $merchant)
                        <option value="{{ $merchant->id }}" @selected($merchantId === $merchant->id)>{{ $merchant->shop_name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="sort" class="sr-only">Sort by</label>
                <select id="sort" name="sort" class="{{ $field }}">
                    <option value="latest" @selected($sort === 'latest')>Newest first</option>
                    <option value="price_asc" @selected($sort === 'price_asc')>Price: low to high</option>
                    <option value="price_desc" @selected($sort === 'price_desc')>Price: high to low</option>
                    <option value="name" @selected($sort === 'name')>Name: A–Z</option>
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="flex-1 rounded-xl bg-(--primary) px-5 py-2.5 text-sm font-semibold text-white transition hover:opacity-90">Apply</button>
                @if ($search !== '' || $merchantId || $sort !== 'latest')
                    <a href="{{ route('products.index') }}" class="rounded-xl border border-gray-200 px-4 py-2.5 text-sm font-semibold transition hover:bg-gray-50">Reset</a>
                @endif
            </div>
        </form>

        <p class="mt-6 text-sm text-(--text)/70">
            @if ($products->total() > 0)
                Showing <span class="font-semibold text-(--text)">{{ $products->firstItem() }}–{{ $products->lastItem() }}</span>
                of <span class="font-semibold text-(--text)">{{ $products->total() }}</span> products
            @else
                No products found
            @endif
        </p>

        @if ($products->isEmpty())
            <div class="mt-6 rounded-2xl border border-dashed border-gray-200 p-12 text-center">
                <p class="text-lg font-bold">Nothing matched your search</p>
                <p class="mt-1 text-sm text-(--text)/60">Try a different keyword or clear the filters.</p>
                <a href="{{ route('products.index') }}" class="mt-5 inline-block rounded-xl bg-(--primary) px-6 py-2.5 text-sm font-semibold text-white">Clear filters</a>
            </div>
        @else
            <div class="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($products as $product)
                    <x-frontend.product-card :product="$product" />
                @endforeach
            </div>

            <div class="mt-10">
                {{ $products->links() }}
            </div>
        @endif
    </section>
</x-frontend.frontendlayout>
