<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $table = 'orders';

    protected $fillable = [
        'user_id',
        'merchant_id',
        'status',
        'payment_status',
        'payment_method',
        'payment_reference',
        'khalti_pidx',
        'total_amount',
        'contact_no',
        'shipping_address',
    ];

    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class, 'merchant_id');
    }

    public function order_items(): HasMany
    {
        return $this->hasMany(Order_item::class, 'order_id');
    }
}
