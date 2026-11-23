<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order_item extends Model
{
    protected $table = 'order_items';

    protected $fillable = [
        'order_id',
        'product_varient_id',
        'qty',
        'product_name',
        'variant_name',
        'unit_price',
        'image_path',
        'cart_line_id',
    ];

    protected function casts(): array
    {
        return [
            'qty' => 'integer',
            'unit_price' => 'decimal:2',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function product_varient(): BelongsTo
    {
        return $this->belongsTo(Product_varient::class, 'product_varient_id');
    }
}
