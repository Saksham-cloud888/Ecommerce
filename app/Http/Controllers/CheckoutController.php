<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Merchant;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Throwable;

class CheckoutController extends Controller
{
    public function show(Request $request, int $merchant): View|RedirectResponse
    {
        $shop = Merchant::query()
            ->whereKey($merchant)
            ->where('status', 'approve')
            ->firstOrFail();

        $items = $request->user()->carts()
            ->where('merchant_id', $shop->id)
            ->whereHas('product_varient.product', fn ($query) => $query->visible())
            ->with('product_varient.product')
            ->orderBy('id')
            ->get();

        if ($items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'There are no available items from this shop to check out.');
        }

        return view('frontend.checkout.show', [
            'merchant' => $shop,
            'items' => $items,
            'subtotal' => $items->sum(fn (Cart $item) => $item->line_total),
        ]);
    }

    public function store(Request $request, int $merchant): RedirectResponse
    {
        $data = $request->validate([
            'contact_no' => ['required', 'string', 'max:40', 'regex:/^[0-9+()\-\s]{7,40}$/'],
            'shipping_address' => ['required', 'string', 'max:2000'],
            'payment_method' => ['required', Rule::in(['cod', 'cash_on_delivery', 'khalti'])],
        ]);

        // Accept the concise "cod" value used by the checkout form while
        // preserving the existing database value for older orders.
        if ($data['payment_method'] === 'cod') {
            $data['payment_method'] = 'cash_on_delivery';
        }

        if ($data['payment_method'] === 'khalti' && blank(config('services.khalti.secret_key'))) {
            return back()
                ->withInput()
                ->withErrors(['payment_method' => 'Khalti is not configured yet. Please choose Cash on Delivery.']);
        }

        $user = $request->user();

        $paymentReference = 'ORD-' . Str::upper(Str::random(12));

        $order = DB::transaction(function () use ($user, $merchant, $data, $paymentReference): Order {
            $shop = Merchant::query()
                ->whereKey($merchant)
                ->where('status', 'approve')
                ->lockForUpdate()
                ->firstOrFail();

            $items = $user->carts()
                ->where('merchant_id', $shop->id)
                ->lockForUpdate()
                ->with(['product_varient.product.merchant'])
                ->orderBy('id')
                ->get();

            $items = $items->filter(fn (Cart $item): bool =>
                $item->product_varient?->product !== null
                && $item->product_varient->product->merchant_id === $shop->id
                && $item->product_varient->product->merchant?->status === 'approve'
            )->values();

            if ($items->isEmpty()) {
                abort(422, 'There are no available items from this shop to order.');
            }

            $total = $items->sum(fn (Cart $item): float => (float) $item->product_varient->final_price * $item->qty);

            $order = Order::create([
                'user_id' => $user->id,
                'merchant_id' => $shop->id,
                'status' => 'pending',
                'payment_status' => 'unpaid',
                'payment_method' => $data['payment_method'],
                'payment_reference' => $paymentReference,
                'total_amount' => $total,
                'contact_no' => $data['contact_no'],
                'shipping_address' => $data['shipping_address'],
            ]);

            foreach ($items as $item) {
                $variant = $item->product_varient;
                $product = $variant->product;

                $order->order_items()->create([
                    'product_varient_id' => $variant->id,
                    'qty' => $item->qty,
                    'product_name' => $product->name,
                    'variant_name' => $variant->name,
                    'unit_price' => $variant->final_price,
                    'image_path' => $variant->img ?: $product->img,
                    'cart_line_id' => $item->id,
                ]);
            }

            if ($data['payment_method'] === 'cash_on_delivery') {
                $user->carts()->whereIn('id', $items->pluck('id'))->delete();
            }

            return $order;
        });

        if ($data['payment_method'] === 'khalti') {
            return $this->initiateKhaltiPayment($order, $user, $data['contact_no']);
        }

        return redirect()
            ->route('orders.show', $order)
            ->with('success', 'Your order was placed. Pay cash on delivery when it arrives.');
    }

    public function khaltiReturn(Request $request): RedirectResponse
    {
        $pidx = $request->query('pidx');
        $reference = $request->query('purchase_order_id');

        if (! is_string($pidx) || ! is_string($reference) || $pidx === '' || $reference === '') {
            return redirect()->route('cart.index')->with('error', 'Khalti did not return a valid payment reference. Your cart is unchanged.');
        }

        $order = Order::query()
            ->where('payment_reference', $reference)
            ->where('payment_method', 'khalti')
            ->first();

        if (! $order || ! hash_equals((string) $order->khalti_pidx, $pidx)) {
            return redirect()->route('cart.index')->with('error', 'We could not verify this Khalti payment. Your cart is unchanged.');
        }

        try {
            $response = $this->khaltiRequest('lookup/', ['pidx' => $pidx]);
        } catch (Throwable $exception) {
            Log::error('Khalti payment verification failed.', [
                'order_id' => $order->id,
                'exception' => $exception->getMessage(),
            ]);

            return $this->redirectToOrderOrLogin($order, 'Payment verification is temporarily unavailable. Your order is still unpaid; please retry or contact support.');
        }

        $verified = $response->successful()
            && ($response->json('status') === 'Completed')
            && ($response->json('pidx') === $pidx)
            && ($response->json('purchase_order_id') === $order->payment_reference)
            && ((int) $response->json('total_amount') === (int) round((float) $order->total_amount * 100));

        if (! $verified) {
            Log::warning('Khalti returned an uncompleted or mismatched payment.', [
                'order_id' => $order->id,
                'status' => $response->json('status'),
            ]);

            return $this->redirectToOrderOrLogin($order, 'Khalti payment was not completed. Your cart is unchanged; you can try again.');
        }

        DB::transaction(function () use ($order): void {
            $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);

            if ($lockedOrder->payment_status === 'paid') {
                return;
            }

            $lockedOrder->update([
                'payment_status' => 'paid',
                'status' => 'processing',
            ]);

            foreach ($lockedOrder->order_items as $orderItem) {
                if (! $orderItem->cart_line_id) {
                    continue;
                }

                $cartLine = Cart::query()
                    ->whereKey($orderItem->cart_line_id)
                    ->where('user_id', $lockedOrder->user_id)
                    ->where('product_varient_id', $orderItem->product_varient_id)
                    ->lockForUpdate()
                    ->first();

                if (! $cartLine) {
                    continue;
                }

                $remainingQty = $cartLine->qty - $orderItem->qty;

                if ($remainingQty > 0) {
                    $cartLine->update(['qty' => $remainingQty]);
                } else {
                    $cartLine->delete();
                }
            }
        });

        $order->refresh();

        return $this->redirectToOrderOrLogin($order, 'Khalti payment successful. Your order is confirmed.');
    }

    private function initiateKhaltiPayment(Order $order, $user, string $contactNo): RedirectResponse
    {
        try {
            $response = $this->khaltiRequest('initiate/', [
                'return_url' => route('payments.khalti.return'),
                'website_url' => config('app.url'),
                'amount' => (int) round((float) $order->total_amount * 100),
                'purchase_order_id' => $order->payment_reference,
                'purchase_order_name' => 'Order #' . $order->id,
                'customer_info' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $contactNo,
                ],
            ]);

            $pidx = $response->json('pidx');
            $paymentUrl = $response->json('payment_url');

            if (! $response->successful() || ! is_string($pidx) || ! is_string($paymentUrl)) {
                Log::error('Khalti payment initiation was rejected.', [
                    'order_id' => $order->id,
                    'http_status' => $response->status(),
                    'response' => $response->json(),
                ]);

                $order->delete();

                return back()->withInput()->withErrors([
                    'payment_method' => 'Khalti could not start this payment. Your cart is unchanged; please retry or choose Cash on Delivery.',
                ]);
            }

            $order->update(['khalti_pidx' => $pidx]);

            return redirect()->away($paymentUrl);
        } catch (Throwable $exception) {
            Log::error('Khalti payment initiation failed.', [
                'order_id' => $order->id,
                'exception' => $exception->getMessage(),
            ]);

            $order->delete();

            return back()->withInput()->withErrors([
                'payment_method' => 'Khalti is temporarily unavailable. Your cart is unchanged; please retry or choose Cash on Delivery.',
            ]);
        }
    }

    private function khaltiRequest(string $endpoint, array $payload): \Illuminate\Http\Client\Response
    {
        $baseUrl = rtrim((string) config('services.khalti.base_url'), '/');

        // Support KHALTI_BASEURL values ending at /api/v2 as in the older
        // integration example, as well as the full /api/v2/epayment base URL.
        if (str_ends_with($baseUrl, '/api/v2')) {
            $baseUrl .= '/epayment';
        }

        return Http::acceptJson()
            ->asJson()
            ->withHeaders(['Authorization' => 'Key ' . config('services.khalti.secret_key')])
            ->timeout(15)
            ->post($baseUrl . '/' . ltrim($endpoint, '/'), $payload);
    }

    private function redirectToOrderOrLogin(Order $order, string $message): RedirectResponse
    {
        if (Auth::guard('web')->check() && Auth::guard('web')->id() === $order->user_id) {
            return redirect()->route('orders.show', $order)->with(
                $order->payment_status === 'paid' ? 'success' : 'error',
                $message,
            );
        }

        return redirect()->route('login')->with('status', $message);
    }
}
