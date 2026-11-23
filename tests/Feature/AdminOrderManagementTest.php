<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Merchant;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class AdminOrderManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_order_list_is_only_available_to_admin_guard(): void
    {
        $this->get(route('filament.admin.resources.orders.index'))
            ->assertRedirect(route('filament.admin.auth.login'));

        $admin = Admin::create([
            'name' => 'Admin User',
            'email' => 'admin@example.test',
            'password' => 'secret',
        ]);

        $this->actingAs($admin, 'admin')
            ->get(route('filament.admin.resources.orders.index'))
            ->assertOk();
    }

    public function test_signed_receipt_requires_admin_authentication(): void
    {
        $user = User::factory()->create();
        $merchant = Merchant::create([
            'name' => 'Shop Owner',
            'email' => 'receipt-owner@example.test',
            'password' => 'secret',
            'shop_name' => 'Receipt Shop',
            'contact' => '9800000000',
            'status' => 'approve',
        ]);
        $order = Order::create([
            'user_id' => $user->id,
            'merchant_id' => $merchant->id,
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'payment_method' => 'cash_on_delivery',
            'total_amount' => 100,
            'contact_no' => '9801111111',
            'shipping_address' => 'Kathmandu',
        ]);
        $receiptUrl = URL::temporarySignedRoute('admin.orders.receipt', now()->addMinutes(10), ['order' => $order->id]);

        $this->get($receiptUrl)->assertRedirect(route('filament.admin.auth.login'));

        $admin = Admin::create([
            'name' => 'Admin User 2',
            'email' => 'admin2@example.test',
            'password' => 'secret',
        ]);

        $this->actingAs($admin, 'admin')
            ->get($receiptUrl)
            ->assertOk()
            ->assertSee('Order receipt')
            ->assertSee('Receipt Shop');
    }

    public function test_customer_sees_approved_status_after_admin_approves_order(): void
    {
        $user = User::factory()->create();
        $merchant = Merchant::create([
            'name' => 'Status Owner',
            'email' => 'status-owner@example.test',
            'password' => 'secret',
            'shop_name' => 'Status Shop',
            'contact' => '9800000000',
            'status' => 'approve',
        ]);
        $order = Order::create([
            'user_id' => $user->id,
            'merchant_id' => $merchant->id,
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'payment_method' => 'cash_on_delivery',
            'total_amount' => 100,
            'contact_no' => '9801111111',
            'shipping_address' => 'Kathmandu',
        ]);

        $admin = Admin::create([
            'name' => 'Status Admin',
            'email' => 'status-admin@example.test',
            'password' => 'secret',
        ]);

        $this->actingAs($admin, 'admin');
        $order->update(['status' => 'approved']);

        $this->actingAs($user, 'web')
            ->get(route('orders.show', $order))
            ->assertOk()
            ->assertSee('Approved')
            ->assertDontSee('Pending');
    }
}
