<x-frontend.frontendlayout :title="'Order History | ' . config('app.name', 'Ecommerce')">
    <section class="bg-gradient-to-br from-(--primary)/10 via-white to-(--secondary)/10">
        <div class="mx-auto max-w-6xl px-4 py-10 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-extrabold sm:text-4xl">Order history</h1>
            <p class="mt-2 text-(--text)/70">View your purchases and delivery status.</p>
        </div>
    </section>

    <section class="mx-auto max-w-6xl px-4 py-10 sm:px-6 lg:px-8">
        @if (session('success'))
            <p role="status" class="mb-6 rounded-xl border border-(--secondary)/30 bg-(--secondary)/10 p-4 text-sm font-medium text-(--secondary)">{{ session('success') }}</p>
        @endif

        @if ($orders->isEmpty())
            <div class="rounded-2xl border border-dashed border-gray-200 p-14 text-center">
                <p class="text-5xl" aria-hidden="true">📦</p>
                <h2 class="mt-4 text-xl font-bold">No orders yet</h2>
                <p class="mt-1 text-sm text-(--text)/60">Your completed checkouts will appear here.</p>
                <a href="{{ route('products.index') }}" class="mt-6 inline-block rounded-xl bg-(--primary) px-7 py-3 font-semibold text-white">Browse products</a>
            </div>
        @else
            <div class="space-y-4">
                @foreach ($orders as $order)
                    <a href="{{ route('orders.show', $order) }}" class="block rounded-2xl border border-gray-100 bg-white p-5 shadow-sm transition hover:border-(--primary)/40 hover:shadow-md sm:p-6">
                        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-(--text)/55">Order #{{ $order->id }}</p>
                                <h2 class="mt-1 text-lg font-bold">{{ $order->merchant?->shop_name ?? 'Shop' }}</h2>
                                <p class="mt-1 text-sm text-(--text)/60">{{ $order->created_at->format('M j, Y · g:i A') }} · {{ $order->order_items_count }} {{ Str::plural('item', $order->order_items_count) }}</p>
                            </div>
                            <div class="flex items-center justify-between gap-4 sm:justify-end">
                                @php
                                    [$statusClass, $statusText] = match ($order->status) {
                                        'approved' => ['bg-emerald-50 text-emerald-800', 'Approved'],
                                        'processing' => ['bg-blue-50 text-blue-800', 'Processing'],
                                        'completed' => ['bg-green-50 text-green-800', 'Completed'],
                                        default => ['bg-amber-50 text-amber-800', 'Pending'],
                                    };
                                @endphp
                                <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $statusClass }}">{{ $statusText }}</span>
                                <span class="text-lg font-extrabold text-(--primary)">Rs. {{ number_format($order->total_amount) }}</span>
                                <span aria-hidden="true" class="text-xl text-(--text)/40">→</span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-8">{{ $orders->links() }}</div>
        @endif
    </section>
</x-frontend.frontendlayout>
