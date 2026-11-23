<footer class="mt-auto bg-(--text) text-gray-300">
    <div class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
        <div class="grid gap-10 sm:grid-cols-2 lg:grid-cols-4">
            <div>
                <a href="{{ route('home') }}" class="inline-block rounded-lg bg-white px-3 py-2 font-extrabold text-(--primary)">
                    {{ config('app.name', 'Nepal Market') }}
                </a>
                <p class="mt-4 text-sm leading-relaxed text-gray-400">
                    A multi-vendor marketplace connecting trusted local merchants with customers across Nepal.
                </p>
            </div>

            <div>
                <h4 class="mb-4 text-sm font-semibold uppercase tracking-wider text-white">Shop</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('products.index') }}" class="transition hover:text-(--primary)">All Products</a></li>
                    <li><a href="{{ route('cart.index') }}" class="transition hover:text-(--primary)">My Cart</a></li>
                    <li><a href="{{ route('orders.index') }}" class="transition hover:text-(--primary)">Order History</a></li>
                    <li><a href="#" class="transition hover:text-(--primary)">Login / Register</a></li>
                </ul>
            </div>

            <div>
                <h4 class="mb-4 text-sm font-semibold uppercase tracking-wider text-white">Merchants</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('merchant.register') }}" class="transition hover:text-(--primary)">Become a Merchant</a></li>
                    <li><a href="{{ url('/merchant') }}" class="transition hover:text-(--primary)">Merchant Login</a></li>
                    <li><a href="{{ route('home') }}#about" class="transition hover:text-(--primary)">About Us</a></li>
                </ul>
            </div>

            <div>
                <h4 class="mb-4 text-sm font-semibold uppercase tracking-wider text-white">Contact</h4>
                <ul class="space-y-2 text-sm text-gray-400">
                    <li>📍 Kathmandu, Nepal</li>
                    <li>📞 +977-9800000000</li>
                    <li>✉️ support@example.com</li>
                </ul>
                <div class="mt-4 inline-flex items-center gap-2 rounded-lg bg-white/10 px-3 py-2 text-xs">
                    <span class="h-2 w-2 rounded-full bg-(--secondary)"></span>
                    Secure payments with Khalti
                </div>
            </div>
        </div>

        <div class="mt-12 flex flex-col items-center justify-between gap-3 border-t border-white/10 pt-6 text-xs text-gray-400 sm:flex-row">
            <p>&copy; {{ date('Y') }} {{ config('app.name', 'Marketplace') }}. All rights reserved.</p>
            <p>Built with Laravel &amp; Filament</p>
        </div>
    </div>
</footer>
