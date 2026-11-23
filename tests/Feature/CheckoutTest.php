<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\Merchant;
use App\Models\Order;
use App\Models\Product;
use App\Models\Product_varient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_view_checkout_for_their_shop_cart(): void
    {
        [$user, $merchant, $variant] = $this->createCartFixture();

        $this->actingAs($user)
            ->get(route('checkout.show', $merchant))
            ->assertOk()
            ->assertSee('Delivery details')
            ->assertSee('Cash on Delivery')
            ->assertSee('Khalti')
            ->assertSee('name="payment_method" value="khalti"', false)
            ->assertSee('Rs. 1,800');
    }

    public function test_customer_can_place_cash_on_delivery_order_and_only_that_shop_cart_is_cleared(): void
    {
        [$user, $merchant, $variant, $cartLine] = $this->createCartFixture();
        $otherMerchant = Merchant::create([
            'name' => 'Second Owner',
            'email' => 'second-owner@example.test',
            'password' => 'password',
            'shop_name' => 'Second Shop',
            'contact' => '9800000001',
            'status' => 'approve',
        ]);
        $otherProduct = Product::create([
            'name' => 'Other Product',
            'slug' => 'other-product',
            'merchant_id' => $otherMerchant->id,
        ]);
        $otherVariant = Product_varient::create([
            'name' => 'Standard',
            'price' => 500,
            'discount' => 0,
            'product_id' => $otherProduct->id,
        ]);
        $otherCartLine = Cart::create([
            'user_id' => $user->id,
            'product_varient_id' => $otherVariant->id,
            'merchant_id' => $otherMerchant->id,
            'qty' => 1,
        ]);

        $this->actingAs($user)
            ->post(route('checkout.store', $merchant), [
                'contact_no' => '9801234567',
                'shipping_address' => 'Kathmandu, Nepal',
                'payment_method' => 'cash_on_delivery',
            ])
            ->assertRedirect();

        $order = Order::query()->where('user_id', $user->id)->firstOrFail();

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'merchant_id' => $merchant->id,
            'payment_method' => 'cash_on_delivery',
            'payment_status' => 'unpaid',
            'total_amount' => 1800,
            'contact_no' => '9801234567',
            'shipping_address' => 'Kathmandu, Nepal',
        ]);
        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_varient_id' => $variant->id,
            'qty' => 2,
            'product_name' => 'Sample Product',
            'variant_name' => 'Standard',
            'unit_price' => 900,
        ]);
        $this->assertDatabaseMissing('carts', ['id' => $cartLine->id]);
        $this->assertDatabaseHas('carts', ['id' => $otherCartLine->id]);
    }

    public function test_khalti_payment_stays_unavailable_without_a_secret_key(): void
    {
        [$user, $merchant] = $this->createCartFixture();
        config(['services.khalti.secret_key' => null]);

        $this->actingAs($user)
            ->from(route('checkout.show', $merchant))
            ->post(route('checkout.store', $merchant), [
                'contact_no' => '9801234567',
                'shipping_address' => 'Kathmandu, Nepal',
                'payment_method' => 'khalti',
            ])
            ->assertRedirect(route('checkout.show', $merchant))
            ->assertSessionHasErrors('payment_method');

        $this->assertDatabaseCount('orders', 0);
    }

    public function test_khalti_payment_redirects_to_gateway_and_confirms_only_after_lookup(): void
    {
        [$user, $merchant, $variant, $cartLine] = $this->createCartFixture();
        config(['services.khalti.secret_key' => 'test_secret_key']);
        config(['services.khalti.base_url' => 'https://khalti.test/api/v2/epayment']);

        Http::fake([
            'https://khalti.test/api/v2/epayment/initiate/' => Http::response([
                'pidx' => 'test-pidx-123',
                'payment_url' => 'https://pay.khalti.test/checkout/test-pidx-123',
            ], 200),
        ]);

        $this->actingAs($user)
            ->post(route('checkout.store', $merchant), [
                'contact_no' => '9801234567',
                'shipping_address' => 'Kathmandu, Nepal',
                'payment_method' => 'khalti',
            ])
            ->assertRedirect('https://pay.khalti.test/checkout/test-pidx-123');

        $order = Order::query()->where('user_id', $user->id)->firstOrFail();
        $this->assertSame('unpaid', $order->payment_status);
        $this->assertSame('test-pidx-123', $order->khalti_pidx);
        $this->assertDatabaseHas('carts', ['id' => $cartLine->id]);

        Http::fake([
            'https://khalti.test/api/v2/epayment/lookup/' => Http::response([
                'pidx' => 'test-pidx-123',
                'status' => 'Completed',
                'purchase_order_id' => $order->payment_reference,
                'total_amount' => 180000,
            ], 200),
        ]);

        $this->actingAs($user)
            ->get(route('payments.khalti.return', [
                'pidx' => 'test-pidx-123',
                'purchase_order_id' => $order->payment_reference,
            ]))
            ->assertRedirect(route('orders.show', $order))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'payment_status' => 'paid',
            'status' => 'processing',
        ]);
        $this->assertDatabaseMissing('carts', ['id' => $cartLine->id]);

        Http::assertSent(fn ($request) => $request->url() === 'https://khalti.test/api/v2/epayment/lookup/'
            && $request['pidx'] === 'test-pidx-123');
    }

    public function test_customer_can_only_view_their_own_order_details(): void
    {
        [$user, $merchant] = $this->createCartFixture();
        $otherUser = User::factory()->create();
        $order = Order::create([
            'user_id' => $user->id,
            'merchant_id' => $merchant->id,
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'payment_method' => 'cash_on_delivery',
            'total_amount' => 100,
            'contact_no' => '9801234567',
            'shipping_address' => 'Somewhere',
        ]);

        $this->actingAs($otherUser)
            ->get(route('orders.show', $order))
            ->assertNotFound();

        $this->actingAs($user)
            ->get(route('orders.index'))
            ->assertOk()
            ->assertSee('Order history');
    }

    private function createCartFixture(): array
    {
        $user = User::factory()->create();
        $merchant = Merchant::create([
            'name' => 'Shop Owner',
            'email' => 'owner@example.test',
            'password' => 'password',
            'shop_name' => 'Approved Shop',
            'contact' => '9800000000',
            'status' => 'approve',
        ]);
        $product = Product::create([
            'name' => 'Sample Product',
            'slug' => 'sample-product',
            'merchant_id' => $merchant->id,
        ]);
        $variant = Product_varient::create([
            'name' => 'Standard',
            'price' => 1000,
            'discount' => 100,
            'product_id' => $product->id,
        ]);
        $cartLine = Cart::create([
            'user_id' => $user->id,
            'product_varient_id' => $variant->id,
            'merchant_id' => $merchant->id,
            'qty' => 2,
        ]);

        return [$user, $merchant, $variant, $cartLine];
    }
}
