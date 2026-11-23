<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_method', 40)->default('cash_on_delivery');
            $table->text('shipping_address')->nullable();
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->string('product_name')->nullable();
            $table->string('variant_name')->nullable();
            $table->decimal('unit_price', 10, 2)->nullable();
            $table->string('image_path')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['product_name', 'variant_name', 'unit_price', 'image_path']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'shipping_address']);
        });
    }
};
