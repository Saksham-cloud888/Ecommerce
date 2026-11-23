<x-frontend.frontendlayout :title="'Order #' . $order->id . ' | ' . config('app.name', 'Ecommerce')">
    @if (session('success') || session('error'))
        <div class="mx-auto max-w-5xl px-4 pt-6 sm:px-6 lg:px-8">
            @if (session('success'))
                <p role="status" class="rounded-xl border border-(--secondary)/30 bg-(--secondary)/10 p-4 text-sm font-medium text-(--secondary)">{{ session('success') }}</p>
            @else
                <p role="alert" class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm font-medium text-amber-900">{{ session('error') }}</p>
            @endif
        </div>
    @endif

    <section class="bg-gradient-to-br from-(--primary)/10 via-white to-(--secondary)/10">
        <div class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8">
            <nav class="text-sm text-(--text)/60" aria-label="Breadcrumb">
                <a href="{{ route('orders.index') }}" class="hover:text-(--primary)">Order history</a>
                <span class="mx-1">/</span>
                <span class="font-medium text-(--text)">Order #{{ $order->id }}</span>
            </nav>
            <div class="mt-3 flex flex-wrap items-end justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-extrabold sm:text-4xl">Order #{{ $order->id }}</h1>
                    <p class="mt-2 text-sm text-(--text)/70">Placed {{ $order->created_at->format('M j, Y · g:i A') }}</p>
                </div>
                @php
                    [$statusClass, $statusText] = match ($order->status) {
                        'approved' => ['bg-emerald-50 text-emerald-800', 'Approved'],
                        'processing' => ['bg-blue-50 text-blue-800', 'Processing'],
                        'completed' => ['bg-green-50 text-green-800', 'Completed'],
                        default => ['bg-amber-50 text-amber-800', 'Pending'],
                    };
                @endphp
                <span class="rounded-full px-4 py-2 text-sm font-semibold {{ $statusClass }}">{{ $statusText }}</span>
            </div>
        </div>
    </section>

    <section class="mx-auto grid max-w-5xl gap-8 px-4 py-10 sm:px-6 lg:grid-cols-5 lg:px-8">
        <div class="overflow-hidden rounded-3xl border border-gray-100 bg-white shadow-sm lg:col-span-3">
            <div class="border-b border-gray-100 px-5 py-4 sm:px-6">
                <h2 class="font-bold">Items from {{ $order->merchant?->shop_name ?? 'shop' }}</h2>
            </div>
            <ul class="divide-y divide-gray-100">
                @foreach ($order->order_items as $item)
                    @php
                        $variant = $item->product_varient;
                        $productName = $item->product_name ?: $variant?->product?->name ?: 'Product';
                        $variantName = $item->variant_name ?: $variant?->name;
                        $unitPrice = $item->unit_price ?? $variant?->final_price ?? 0;
                        $image = $item->image_path ? asset('storage/' . $item->image_path) : ($variant?->image_url ?: $variant?->product?->image_url);
                    @endphp
                    <li class="flex items-center gap-4 p-5 sm:p-6">
                        <div class="h-16 w-16 shrink-0 overflow-hidden rounded-xl bg-gradient-to-br from-(--primary)/10 to-(--secondary)/10">
                            @if ($image)
                                <img src="{{ $image }}" alt="{{ $productName }}" class="h-full w-full object-cover">
                            @else
                                <span class="flex h-full w-full items-center justify-center text-2xl font-bold text-(--primary)/50" aria-hidden="true">{{ mb_strtoupper(mb_substr($productName, 0, 1)) }}</span>
                            @endif
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate font-semibold">{{ $productName }}</p>
                            @if ($variantName)<p class="text-sm text-(--text)/60">{{ $variantName }}</p>@endif
                            <p class="text-sm text-(--text)/60">Qty {{ $item->qty }} × Rs. {{ number_format($unitPrice) }}</p>
                        </div>
                        <p class="shrink-0 font-bold">Rs. {{ number_format((float) $unitPrice * $item->qty) }}</p>
                    </li>
                @endforeach
            </ul>
            <div class="flex justify-between border-t border-gray-100 bg-gray-50 px-5 py-5 text-lg font-extrabold sm:px-6">
                <span>Total</span>
                <span class="text-(--primary)">Rs. {{ number_format($order->total_amount) }}</span>
            </div>
        </div>

        <aside class="h-fit rounded-3xl border border-gray-100 bg-white p-6 shadow-sm lg:col-span-2">
            <h2 class="text-lg font-bold">Delivery & payment</h2>
            <dl class="mt-4 space-y-4 text-sm">
                <div>
                    <dt class="text-(--text)/55">Payment method</dt>
                    <dd class="mt-1 font-semibold">{{ Str::headline($order->payment_method ?? 'cash_on_delivery') }}</dd>
                </div>
                <div>
                    <dt class="text-(--text)/55">Payment status</dt>
                    <dd class="mt-1 font-semibold">{{ Str::headline($order->payment_status) }}</dd>
                </div>
                <div>
                    <dt class="text-(--text)/55">Contact number</dt>
                    <dd class="mt-1 font-semibold">{{ $order->contact_no }}</dd>
                </div>
                @if ($order->shipping_address)
                    <div>
                        <dt class="text-(--text)/55">Delivery address</dt>
                        <dd class="mt-1 whitespace-pre-line font-semibold">{{ $order->shipping_address }}</dd>
                    </div>
                @endif
            </dl>
            <a href="{{ route('orders.index') }}" class="mt-6 inline-block font-semibold text-(--secondary) hover:underline">← Back to order history</a>
        </aside>
    </section>
</x-frontend.frontendlayout>
