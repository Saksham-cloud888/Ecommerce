<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product_varient extends Model
{
    protected $table = 'product_varients';

    protected $fillable = [
        'name',
        'price',
        'discount',
        'img',
        'product_id',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'discount' => 'decimal:2',
        ];
    }

    /** Price after the flat discount. */
    protected function finalPrice(): Attribute
    {
        return Attribute::get(fn () => max(0, (float) $this->price - (float) $this->discount));
    }

    protected function discountPercent(): Attribute
    {
        return Attribute::get(function () {
            $price = (float) $this->price;
            $discount = (float) $this->discount;

            return $price > 0 && $discount > 0 ? (int) round($discount / $price * 100) : 0;
        });
    }

    protected function imageUrl(): Attribute
    {
        return Attribute::get(fn () => $this->img ? asset('storage/' . $this->img) : null);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class, 'product_varient_id');
    }

    public function order_items(): HasMany
    {
        return $this->hasMany(Order_item::class, 'product_varient_id');
    }
}
