<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_reference')->nullable()->unique();
            $table->string('khalti_pidx')->nullable()->unique();
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->unsignedBigInteger('cart_line_id')->nullable()->index();
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropIndex(['cart_line_id']);
            $table->dropColumn('cart_line_id');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropUnique(['payment_reference']);
            $table->dropUnique(['khalti_pidx']);
            $table->dropColumn(['payment_reference', 'khalti_pidx']);
        });
    }
};
