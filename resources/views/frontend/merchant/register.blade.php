<x-frontend.frontendlayout :title="'Become a Merchant | ' . config('app.name', 'Ecommerce')">
    <section class="bg-gradient-to-br from-(--primary)/10 via-white to-(--secondary)/10 py-12 lg:py-20">
        <div class="mx-auto grid max-w-6xl items-start gap-10 px-4 sm:px-6 lg:grid-cols-5 lg:px-8">
            <div class="lg:col-span-2 lg:pt-6">
                <span class="inline-block rounded-full bg-(--primary)/10 px-4 py-1 text-sm font-semibold text-(--primary)">Merchant Registration</span>
                <h1 class="mt-4 text-3xl font-extrabold leading-tight sm:text-4xl">Grow your business with us</h1>
                <p class="mt-4 text-(--text)/80">Apply to become a merchant. Our admin team will review your request and email you once approved.</p>

                <ul class="mt-8 space-y-5">
                    @foreach ([['📝', 'Submit your request', 'Tell us about you and your shop.'], ['🔍', 'Admin reviews it', 'We verify your details, usually within 1–2 days.'], ['📧', 'Get an approval email', 'Then log in to your merchant dashboard.'], ['🛍️', 'Start selling', 'Add products and manage your orders.']] as [$icon, $title, $desc])
                        <li class="flex gap-4">
                            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-white text-xl shadow-sm">{{ $icon }}</span>
                            <div>
                                <h2 class="font-semibold">{{ $title }}</h2>
                                <p class="text-sm text-(--text)/70">{{ $desc }}</p>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="rounded-3xl bg-white p-6 shadow-xl sm:p-10 lg:col-span-3">
                <h2 class="text-2xl font-bold">Create merchant account</h2>
                <p class="mt-1 text-sm text-(--text)/70">Fields marked with * are required.</p>

                @if (session('success'))
                    <div class="mt-5 rounded-xl border border-(--secondary)/30 bg-(--secondary)/10 p-4 text-sm font-medium text-(--secondary)">
                        {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('merchant.register.store') }}" method="POST" class="mt-6 grid gap-5 sm:grid-cols-2">
                    @csrf
                    @php
                        $input = 'mt-1.5 w-full rounded-xl border border-gray-200 px-4 py-3 text-sm outline-none transition placeholder:text-gray-400 focus:border-(--primary) focus:ring-4 focus:ring-(--primary)/15';
                    @endphp

                    <div>
                        <label for="name" class="text-sm font-semibold">Owner name *</label>
                        <input id="name" name="name" type="text" value="{{ old('name') }}" required maxlength="255" placeholder="Ram Sharma" class="{{ $input }}">
                        @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="shop_name" class="text-sm font-semibold">Shop name *</label>
                        <input id="shop_name" name="shop_name" type="text" value="{{ old('shop_name') }}" required maxlength="255" placeholder="Himalayan Weaves" class="{{ $input }}">
                        @error('shop_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="email" class="text-sm font-semibold">Email *</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required maxlength="255" placeholder="you@example.com" class="{{ $input }}">
                        @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="contact" class="text-sm font-semibold">Contact number *</label>
                        <input id="contact" name="contact" type="tel" value="{{ old('contact') }}" required maxlength="255" placeholder="98XXXXXXXX" class="{{ $input }}">
                        @error('contact') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <label class="flex items-start gap-3 text-sm sm:col-span-2">
                        <input type="checkbox" name="terms" value="1" required @checked(old('terms')) class="mt-0.5 h-4 w-4 rounded border-gray-300 accent-(--primary)">
                        <span>I agree to the merchant terms and understand my account must be approved by admin before I can log in.</span>
                    </label>
                    @error('terms') <p class="-mt-4 text-xs text-red-600 sm:col-span-2">{{ $message }}</p> @enderror

                    <div class="sm:col-span-2">
                        <button type="submit" class="w-full rounded-xl bg-(--primary) px-6 py-3.5 font-bold text-white shadow-lg shadow-(--primary)/30 transition hover:opacity-95">Submit Request</button>
                        <p class="mt-4 text-center text-sm text-(--text)/70">
                            Already approved?
                            <a href="{{ url('/merchant') }}" class="font-semibold text-(--secondary) hover:underline">Merchant login</a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </section>
</x-frontend.frontendlayout>
