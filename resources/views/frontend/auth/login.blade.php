<x-frontend.frontendlayout :title="'Login | ' . config('app.name', 'Ecommerce')">
    <section class="bg-gradient-to-br from-(--primary)/10 via-white to-(--secondary)/10 py-12 lg:py-20">
        <div class="mx-auto max-w-md px-4 sm:px-6">
            <div class="rounded-3xl bg-white p-6 shadow-xl sm:p-10">
                <div class="text-center">
                    <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-(--primary)/10 text-2xl" aria-hidden="true">🛍️</span>
                    <h1 class="mt-5 text-3xl font-extrabold">Welcome back</h1>
                    <p class="mt-2 text-sm text-(--text)/70">Sign in to shop, track your orders and pay securely with Khalti.</p>
                </div>

                @if (session('error') || $errors->any())
                    <div role="alert" class="mt-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm font-medium text-red-700">
                        {{ session('error') ?? $errors->first() }}
                    </div>
                @endif

                @if (session('success') || session('status'))
                    <div role="status" class="mt-6 rounded-xl border border-(--secondary)/30 bg-(--secondary)/10 p-4 text-sm font-medium text-(--secondary)">
                        {{ session('success') ?? session('status') }}
                    </div>
                @endif

                <a href="{{ route('auth.google') }}" class="mt-8 flex w-full items-center justify-center gap-3 rounded-xl border border-gray-300 bg-white px-6 py-3.5 text-sm font-semibold shadow-sm transition hover:border-gray-400 hover:bg-gray-50 hover:shadow focus:outline-none focus-visible:ring-4 focus-visible:ring-(--primary)/20">
                    <svg class="h-5 w-5" viewBox="0 0 48 48" aria-hidden="true">
                        <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                        <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
                        <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/>
                        <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
                    </svg>
                    Continue with Google
                </a>

                <div class="my-6 flex items-center gap-3 text-xs text-(--text)/50">
                    <span class="h-px flex-1 bg-gray-200"></span>
                    <span>Secure sign-in</span>
                    <span class="h-px flex-1 bg-gray-200"></span>
                </div>

                <ul class="space-y-3 text-sm text-(--text)/80">
                    @foreach (['No password to remember', 'Your account is created automatically on first sign-in', 'We use your verified Google name and email'] as $point)
                        <li class="flex items-start gap-3">
                            <span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-(--secondary) text-[10px] text-white">✓</span>
                            <span>{{ $point }}</span>
                        </li>
                    @endforeach
                </ul>

                <p class="mt-8 text-center text-xs leading-relaxed text-(--text)/60">By signing in, you agree to our Terms of Service and Privacy Policy.</p>
            </div>

            <p class="mt-6 text-center text-sm text-(--text)/70">
                Selling on {{ config('app.name', 'our marketplace') }}?
                <a href="{{ url('/merchant/login') }}" class="font-semibold text-(--secondary) hover:underline">Merchant login</a>
                <span class="mx-1">·</span>
                <a href="{{ route('merchant.register') }}" class="font-semibold text-(--secondary) hover:underline">Apply as merchant</a>
            </p>
        </div>
    </section>
</x-frontend.frontendlayout>
