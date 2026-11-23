<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cart extends Model
{
    public const MAX_QTY = 10;

    protected $table = 'carts';

    protected $fillable = [
        'user_id',
        'product_varient_id',
        'merchant_id',
        'qty',
    ];

    protected function casts(): array
    {
        return [
            'qty' => 'integer',
        ];
    }

    protected function unitPrice(): Attribute
    {
        return Attribute::get(fn () => $this->product_varient?->final_price ?? 0);
    }

    protected function lineTotal(): Attribute
    {
        return Attribute::get(fn () => $this->unit_price * $this->qty);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function product_varient(): BelongsTo
    {
        return $this->belongsTo(Product_varient::class, 'product_varient_id');
    }

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class, 'merchant_id');
    }
}
