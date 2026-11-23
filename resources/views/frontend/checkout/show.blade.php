<x-frontend.frontendlayout :title="'Checkout | ' . config('app.name', 'Ecommerce')">
    <section class="bg-gradient-to-br from-(--primary)/10 via-white to-(--secondary)/10">
        <div class="mx-auto max-w-6xl px-4 py-10 sm:px-6 lg:px-8">
            <nav class="text-sm text-(--text)/60" aria-label="Breadcrumb">
                <a href="{{ route('cart.index') }}" class="hover:text-(--primary)">Cart</a>
                <span class="mx-1">/</span>
                <span class="font-medium text-(--text)">Checkout</span>
            </nav>
            <h1 class="mt-3 text-3xl font-extrabold sm:text-4xl">Checkout</h1>
            <p class="mt-2 text-(--text)/70">Ordering from {{ $merchant->shop_name }}. Shops are checked out separately.</p>
        </div>
    </section>

    <section class="mx-auto grid max-w-6xl gap-8 px-4 py-10 sm:px-6 lg:grid-cols-5 lg:px-8">
        <div class="lg:col-span-3">
            <form action="{{ route('checkout.store', $merchant) }}" method="POST" class="space-y-7">
                @csrf
                @if ($errors->any())
                    <div role="alert" class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">{{ $errors->first() }}</div>
                @endif

                <section class="rounded-3xl border border-gray-100 bg-white p-6 shadow-sm sm:p-8">
                    <h2 class="text-xl font-bold">Delivery details</h2>
                    <div class="mt-5 space-y-5">
                        <div>
                            <label for="contact_no" class="text-sm font-semibold">Contact number *</label>
                            <input id="contact_no" name="contact_no" type="tel" value="{{ old('contact_no', auth()->user()->phone ?? '') }}" required maxlength="40" autocomplete="tel" placeholder="98XXXXXXXX" class="mt-1.5 w-full rounded-xl border border-gray-200 px-4 py-3 text-sm outline-none transition focus:border-(--primary) focus:ring-4 focus:ring-(--primary)/15">
                            @error('contact_no') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="shipping_address" class="text-sm font-semibold">Delivery address *</label>
                            <textarea id="shipping_address" name="shipping_address" rows="4" required maxlength="2000" autocomplete="street-address" placeholder="Street, area, city, district" class="mt-1.5 w-full rounded-xl border border-gray-200 px-4 py-3 text-sm outline-none transition focus:border-(--primary) focus:ring-4 focus:ring-(--primary)/15">{{ old('shipping_address') }}</textarea>
                            @error('shipping_address') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </section>

                <section class="rounded-3xl border border-gray-100 bg-white p-6 shadow-sm sm:p-8">
                    <h2 class="text-xl font-bold">Payment method</h2>
                    <div class="mt-5 space-y-3">
                        <label class="flex cursor-pointer items-start gap-3 rounded-2xl border-2 border-(--primary) bg-(--primary)/5 p-4">
                            <input type="radio" name="payment_method" value="cod" @checked(old('payment_method', 'cod') === 'cod') required class="mt-1 accent-(--primary)">
                            <span>
                                <span class="block font-semibold">Cash on Delivery</span>
                                <span class="mt-1 block text-sm text-(--text)/65">Pay the merchant when your order arrives.</span>
                            </span>
                        </label>
                        <label class="flex cursor-pointer items-start gap-3 rounded-2xl border border-gray-200 p-4 transition has-[:checked]:border-(--primary) has-[:checked]:bg-(--primary)/5">
                            <input type="radio" name="payment_method" value="khalti" @checked(old('payment_method') === 'khalti') required class="mt-1 accent-(--primary)">
                            <span>
                                <span class="block font-semibold">Khalti</span>
                                <span class="mt-1 block text-sm text-(--text)/65">Pay securely online with Khalti.</span>
                            </span>
                        </label>
                    </div>
                </section>

                <button type="submit" class="w-full rounded-xl bg-(--primary) px-6 py-3.5 font-bold text-white shadow-lg shadow-(--primary)/30 transition hover:opacity-95">Place order / Continue to payment</button>
            </form>
        </div>

        <aside class="h-fit rounded-3xl border border-gray-100 bg-white p-6 shadow-sm lg:col-span-2">
            <h2 class="text-xl font-bold">Order summary</h2>
            <p class="mt-1 text-sm text-(--text)/65">{{ $merchant->shop_name }}</p>
            <ul class="mt-5 divide-y divide-gray-100">
                @foreach ($items as $item)
                    @php
                        $variant = $item->product_varient;
                        $product = $variant->product;
                    @endphp
                    <li class="flex justify-between gap-4 py-4 text-sm">
                        <span class="min-w-0">
                            <span class="block truncate font-semibold">{{ $product->name }}</span>
                            <span class="text-(--text)/60">{{ $variant->name }} × {{ $item->qty }}</span>
                        </span>
                        <span class="shrink-0 font-semibold">Rs. {{ number_format($item->line_total) }}</span>
                    </li>
                @endforeach
            </ul>
            <div class="mt-4 flex justify-between border-t border-gray-100 pt-4 text-lg font-extrabold">
                <span>Total</span>
                <span class="text-(--primary)">Rs. {{ number_format($subtotal) }}</span>
            </div>
            <p class="mt-3 text-xs leading-relaxed text-(--text)/60">Payment will use the method selected above. Khalti orders are confirmed only after the server verifies payment with Khalti.</p>
        </aside>
    </section>
</x-frontend.frontendlayout>
