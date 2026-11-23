<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class OrderReceiptController extends Controller
{
    public function __invoke(Request $request, int $order): View|RedirectResponse
    {
        abort_unless($request->hasValidSignature(), 403);

        if (! Auth::guard('admin')->check()) {
            return redirect()->route('filament.admin.auth.login');
        }

        $order = Order::query()
            ->with(['user:id,name,email', 'merchant:id,shop_name,email,contact', 'order_items'])
            ->findOrFail($order);

        return view('admin.orders.receipt', compact('order'));
    }
}
