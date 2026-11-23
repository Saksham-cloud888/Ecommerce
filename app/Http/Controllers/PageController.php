<?php

namespace App\Http\Controllers;

use App\Mail\MerchantRegistrationNotification;
use App\Models\Admin;
use App\Models\Merchant;
use App\Models\Product;
use App\Models\Product_varient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PageController extends Controller
{
    public function home()
    {
        $products = Product::visible()
            ->with(['merchant:id,shop_name', 'product_varients'])
            ->latest('id')
            ->take(6)
            ->get();

        $stats = [
            'merchants' => Merchant::where('status', 'approve')->count(),
            'products' => Product::visible()->count(),
            'variants' => Product_varient::whereHas('product', fn ($query) => $query->visible())->count(),
        ];

        return view('frontend.home', compact('products', 'stats'));
    }

    public function products(Request $request)
    {
        $search = mb_substr(trim((string) $request->query('q', '')), 0, 100);
        $merchantId = $request->integer('merchant') ?: null;
        $sort = $request->query('sort', 'latest');

        if (! in_array($sort, ['latest', 'price_asc', 'price_desc', 'name'], true)) {
            $sort = 'latest';
        }

        $query = Product::visible()
            ->select('products.*')
            ->addSelect([
                'min_final_price' => Product_varient::query()
                    ->selectRaw('MIN(price - discount)')
                    ->whereColumn('product_varients.product_id', 'products.id'),
            ])
            ->with(['merchant:id,shop_name', 'product_varients'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('products.name', 'like', "%{$search}%")
                        ->orWhere('products.description', 'like', "%{$search}%")
                        ->orWhereHas('merchant', fn ($merchantQuery) => $merchantQuery->where('shop_name', 'like', "%{$search}%"));
                });
            })
            ->when($merchantId, fn ($query) => $query->where('products.merchant_id', $merchantId));

        match ($sort) {
            'price_asc' => $query->orderBy('min_final_price')->orderByDesc('products.id'),
            'price_desc' => $query->orderByDesc('min_final_price')->orderByDesc('products.id'),
            'name' => $query->orderBy('products.name'),
            default => $query->orderByDesc('products.id'),
        };

        $products = $query->paginate(9)->withQueryString();
        $merchants = Merchant::where('status', 'approve')
            ->whereHas('products.product_varients')
            ->orderBy('shop_name')
            ->get(['id', 'shop_name']);

        return view('frontend.products.index', compact('products', 'merchants', 'search', 'merchantId', 'sort'));
    }

    public function productShow(string $slug)
    {
        $product = Product::visible()
            ->with(['merchant:id,shop_name', 'product_varients'])
            ->where('slug', $slug)
            ->firstOrFail();

        $related = Product::visible()
            ->with(['merchant:id,shop_name', 'product_varients'])
            ->where('products.id', '!=', $product->id)
            ->orderByRaw('products.merchant_id = ? desc', [$product->merchant_id])
            ->orderByDesc('products.id')
            ->take(4)
            ->get();

        return view('frontend.products.show', compact('product', 'related'));
    }

    public function merchantRegister()
    {
        return view('frontend.merchant.register');
    }

    public function storeMerchant(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'shop_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email:rfc', 'max:255', 'unique:merchants,email'],
            'contact' => ['required', 'string', 'regex:/^[0-9+\-\s]{7,15}$/'],
            'terms' => ['accepted'],
        ], [
            'email.unique' => 'A merchant request with this email already exists.',
            'contact.regex' => 'Please enter a valid contact number.',
            'terms.accepted' => 'You must accept the merchant terms.',
        ]);

        $merchant = Merchant::create([
            'name' => $validated['name'],
            'shop_name' => $validated['shop_name'],
            'email' => $validated['email'],
            'contact' => $validated['contact'],
            'status' => 'pending',
        ]);

        try {
            $adminEmails = Admin::query()
                ->whereNotNull('email')
                ->pluck('email')
                ->filter()
                ->unique()
                ->values()
                ->all();

            if ($adminEmails === []) {
                Log::warning('Merchant registration saved, but no admin email recipients were found.', [
                    'merchant_id' => $merchant->id,
                ]);
            } else {
                Mail::to($adminEmails)->send(new MerchantRegistrationNotification($merchant));
            }
        } catch (\Throwable $exception) {
            Log::error('Merchant registration email could not be sent: ' . $exception->getMessage(), [
                'merchant_id' => $merchant->id,
            ]);
        }

        return redirect()
            ->route('merchant.register')
            ->with('success', 'Your request has been submitted for review. We will email you once an admin approves your shop.');
    }
}
