<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $orders = $request->user()->orders()
            ->with('merchant:id,shop_name')
            ->withCount('order_items')
            ->latest('id')
            ->paginate(10);

        return view('frontend.orders.index', compact('orders'));
    }

    public function show(Request $request, int $order): View
    {
        $order = $request->user()->orders()
            ->with(['merchant:id,shop_name', 'order_items.product_varient.product'])
            ->findOrFail($order);

        return view('frontend.orders.show', compact('order'));
    }
}
