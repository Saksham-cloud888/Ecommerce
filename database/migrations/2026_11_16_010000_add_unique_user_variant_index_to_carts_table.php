<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $duplicates = DB::table('carts')
            ->select('user_id', 'product_varient_id')
            ->groupBy('user_id', 'product_varient_id')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($duplicates as $duplicate) {
            $lines = DB::table('carts')
                ->where('user_id', $duplicate->user_id)
                ->where('product_varient_id', $duplicate->product_varient_id)
                ->orderBy('id')
                ->get();

            $keep = $lines->first();
            $quantity = min(10, $lines->sum('qty'));

            DB::table('carts')->where('id', $keep->id)->update([
                'qty' => $quantity,
                'updated_at' => now(),
            ]);

            DB::table('carts')->whereIn('id', $lines->skip(1)->pluck('id'))->delete();
        }

        Schema::table('carts', function (Blueprint $table) {
            $table->unique(['user_id', 'product_varient_id'], 'carts_user_variant_unique');
        });
    }

    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->dropUnique('carts_user_variant_unique');
        });
    }
};
