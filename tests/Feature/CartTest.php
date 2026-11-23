<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\Merchant;
use App\Models\Product;
use App\Models\Product_varient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_google_login_when_opening_cart(): void
    {
        $this->get(route('cart.index'))
            ->assertRedirect(route('login'));
    }

    public function test_customer_can_add_a_visible_variant_to_their_cart(): void
    {
        $user = User::factory()->create();
        $merchant = Merchant::create([
            'name' => 'Shop Owner',
            'email' => 'owner@example.test',
            'password' => 'test-password',
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

        $this->actingAs($user)
            ->from(route('products.show', $product->slug))
            ->post(route('cart.store'), [
                'variant_id' => $variant->id,
                'qty' => 2,
            ])
            ->assertRedirect(route('products.show', $product->slug))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('carts', [
            'user_id' => $user->id,
            'product_varient_id' => $variant->id,
            'merchant_id' => $merchant->id,
            'qty' => 2,
        ]);

        $this->actingAs($user)
            ->get(route('cart.index'))
            ->assertOk()
            ->assertSee('Approved Shop')
            ->assertSee('Rs. 1,800');
    }

    public function test_customer_cannot_update_another_users_cart_line(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $merchant = Merchant::create([
            'name' => 'Shop Owner',
            'email' => 'owner2@example.test',
            'password' => 'test-password',
            'shop_name' => 'Another Shop',
            'contact' => '9800000001',
            'status' => 'approve',
        ]);
        $product = Product::create([
            'name' => 'Another Product',
            'slug' => 'another-product',
            'merchant_id' => $merchant->id,
        ]);
        $variant = Product_varient::create([
            'name' => 'Standard',
            'price' => 500,
            'discount' => 0,
            'product_id' => $product->id,
        ]);
        $line = Cart::create([
            'user_id' => $owner->id,
            'product_varient_id' => $variant->id,
            'merchant_id' => $merchant->id,
            'qty' => 1,
        ]);

        $this->actingAs($otherUser)
            ->patch(route('cart.update', $line), ['qty' => 5])
            ->assertNotFound();

        $this->assertDatabaseHas('carts', [
            'id' => $line->id,
            'user_id' => $owner->id,
            'qty' => 1,
        ]);
    }
}
