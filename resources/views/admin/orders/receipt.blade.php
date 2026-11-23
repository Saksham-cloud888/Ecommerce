<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Order #{{ $order->id }} receipt</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; padding: 32px; color: #1f2937; font: 14px/1.5 Arial, sans-serif; }
        .receipt { max-width: 760px; margin: 0 auto; }
        .top { display: flex; justify-content: space-between; gap: 24px; border-bottom: 2px solid #111827; padding-bottom: 20px; }
        h1, h2, p { margin-top: 0; }
        h1 { margin-bottom: 4px; font-size: 26px; }
        .muted { color: #6b7280; }
        .details { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin: 24px 0; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px 8px; border-bottom: 1px solid #e5e7eb; text-align: left; }
        th:last-child, td:last-child { text-align: right; }
        .total { margin: 18px 0 0 auto; width: min(320px, 100%); }
        .total div { display: flex; justify-content: space-between; padding: 5px 0; }
        .grand { border-top: 2px solid #111827; margin-top: 6px; padding-top: 10px !important; font-size: 18px; font-weight: bold; }
        .print { margin-top: 24px; padding: 10px 18px; border: 0; border-radius: 8px; background: #f85f11; color: white; font-weight: bold; cursor: pointer; }
        @media print { body { padding: 0; } .print { display: none; } }
        @media (max-width: 560px) { body { padding: 18px; } .top { display: block; } .details { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <main class="receipt">
        <header class="top">
            <div>
                <h1>{{ config('app.name', 'Marketplace') }}</h1>
                <p class="muted">Order receipt</p>
            </div>
            <div>
                <strong>Order #{{ $order->id }}</strong><br>
                <span class="muted">{{ $order->created_at->format('M j, Y g:i A') }}</span>
            </div>
        </header>

        <section class="details">
            <div>
                <h2>Customer</h2>
                <p>{{ $order->user?->name }}<br>{{ $order->user?->email }}<br>{{ $order->contact_no }}</p>
                @if ($order->shipping_address)
                    <p class="muted">{{ $order->shipping_address }}</p>
                @endif
            </div>
            <div>
                <h2>Merchant</h2>
                <p>{{ $order->merchant?->shop_name }}<br>{{ $order->merchant?->email }}<br>{{ $order->merchant?->contact }}</p>
                <p><strong>Payment:</strong> {{ \Illuminate\Support\Str::headline($order->payment_method ?? 'cash_on_delivery') }}<br>
                    <strong>Payment status:</strong> {{ \Illuminate\Support\Str::headline($order->payment_status) }}<br>
                    <strong>Order status:</strong> {{ \Illuminate\Support\Str::headline($order->status) }}</p>
            </div>
        </section>

        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Qty</th>
                    <th>Unit price</th>
                    <th>Line total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->order_items as $item)
                    @php
                        $name = $item->product_name ?: $item->product_varient?->product?->name ?: 'Product';
                        $variant = $item->variant_name ?: $item->product_varient?->name;
                        $price = (float) ($item->unit_price ?? $item->product_varient?->final_price ?? 0);
                    @endphp
                    <tr>
                        <td>{{ $name }}@if ($variant)<br><span class="muted">{{ $variant }}</span>@endif</td>
                        <td>{{ $item->qty }}</td>
                        <td>Rs. {{ number_format($price, 2) }}</td>
                        <td>Rs. {{ number_format($price * $item->qty, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <section class="total">
            <div class="grand"><span>Total</span><span>Rs. {{ number_format($order->total_amount, 2) }}</span></div>
        </section>

        <button class="print" onclick="window.print()" type="button">Print receipt</button>
    </main>
</body>
</html>
