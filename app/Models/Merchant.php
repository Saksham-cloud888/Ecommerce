<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Merchant extends Authenticatable
{
    use Notifiable;

    protected $table = 'merchants';

    protected $fillable = [
        'name',
        'email',
        'password',
        'shop_name',
        'contact',
        'status',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'merchant_id');
    }

    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class, 'merchant_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'merchant_id');
    }
}
