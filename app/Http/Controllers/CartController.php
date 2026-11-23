<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product_varient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $lines = $request->user()->carts()
            ->with(['merchant:id,shop_name,status', 'product_varient.product'])
            ->orderBy('id')
            ->get();

        [$available, $unavailable] = $lines->partition(
            fn (Cart $line): bool => $line->product_varient?->product !== null
                && $line->merchant?->status === 'approve'
        );

        $groups = $available->groupBy('merchant_id')->map(fn ($items) => [
            'merchant' => $items->first()->merchant,
            'items' => $items,
            'quantity' => $items->sum('qty'),
            'subtotal' => $items->sum(fn (Cart $line) => $line->line_total),
        ]);

        return view('frontend.cart.index', compact('groups', 'unavailable'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'variant_id' => ['required', 'integer'],
            'qty' => ['required', 'integer', 'min:1', 'max:' . Cart::MAX_QTY],
        ]);

        $variant = Product_varient::query()
            ->whereKey($data['variant_id'])
            ->whereHas('product', fn ($query) => $query->visible())
            ->with('product:id,merchant_id')
            ->first();

        if (! $variant) {
            return back()->with('error', 'This item is no longer available.');
        }

        $user = $request->user();
        $capped = false;

        DB::transaction(function () use ($user, $variant, $data, &$capped): void {
            $line = Cart::query()
                ->where('user_id', $user->id)
                ->where('product_varient_id', $variant->id)
                ->lockForUpdate()
                ->first();

            if ($line) {
                $newQuantity = $line->qty + $data['qty'];
                $capped = $newQuantity > Cart::MAX_QTY;
                $line->update(['qty' => min($newQuantity, Cart::MAX_QTY)]);

                return;
            }

            Cart::create([
                'user_id' => $user->id,
                'product_varient_id' => $variant->id,
                'merchant_id' => $variant->product->merchant_id,
                'qty' => $data['qty'],
            ]);
        });

        return back()->with('success', $capped
            ? 'Added to your cart (maximum ' . Cart::MAX_QTY . ' per item).'
            : 'Added to your cart.');
    }

    public function update(Request $request, int $cart): RedirectResponse
    {
        $data = $request->validate([
            'qty' => ['required', 'integer', 'min:1', 'max:' . Cart::MAX_QTY],
        ]);

        $request->user()->carts()->findOrFail($cart)->update(['qty' => $data['qty']]);

        return back();
    }

    public function destroy(Request $request, int $cart): RedirectResponse
    {
        $request->user()->carts()->whereKey($cart)->delete();

        return back()->with('success', 'Item removed from your cart.');
    }
}
