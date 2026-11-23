<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $table = 'products';

    protected $fillable = [
        'name',
        'slug',
        'img',
        'description',
        'merchant_id',
    ];

    /** Only products from approved merchants that have at least one variant. */
    public function scopeVisible(Builder $query): Builder
    {
        return $query
            ->whereHas('merchant', fn (Builder $merchantQuery) => $merchantQuery->where('status', 'approve'))
            ->whereHas('product_varients');
    }

    protected function imageUrl(): Attribute
    {
        return Attribute::get(fn () => $this->img ? asset('storage/' . $this->img) : null);
    }

    /** Get the variant with the lowest after-discount price. */
    protected function cheapestVariant(): Attribute
    {
        return Attribute::get(
            fn () => $this->product_varients
                ->sortBy(fn (Product_varient $variant) => $variant->final_price)
                ->first(),
        );
    }

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class, 'merchant_id');
    }

    public function product_varients(): HasMany
    {
        return $this->hasMany(Product_varient::class, 'product_id');
    }
}
