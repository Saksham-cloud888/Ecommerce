<?php

namespace Database\Seeders;

use App\Models\Merchant;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $catalog = [
            'sita@himalayanweaves.test' => [
                [
                    'name' => 'Pure Pashmina Shawl',
                    'slug' => 'pure-pashmina-shawl',
                    'description' => 'Soft, handwoven pashmina shawl made from fine Himalayan wool.',
                    'variants' => [
                        ['name' => 'Black',    'price' => 5500, 'discount' => 1000],
                        ['name' => 'Maroon',   'price' => 5500, 'discount' => 1000],
                        ['name' => 'Ivory',    'price' => 5800, 'discount' => 800],
                    ],
                ],
                [
                    'name' => 'Woolen Winter Cap',
                    'slug' => 'woolen-winter-cap',
                    'description' => 'Warm hand-knitted woolen cap with a fleece lining.',
                    'variants' => [
                        ['name' => 'Medium', 'price' => 900,  'discount' => 100],
                        ['name' => 'Large',  'price' => 950,  'discount' => 100],
                    ],
                ],
                [
                    'name' => 'Handwoven Dhaka Topi',
                    'slug' => 'handwoven-dhaka-topi',
                    'description' => 'Traditional Nepali Dhaka topi, woven by artisans in Palpa.',
                    'variants' => [
                        ['name' => 'Classic', 'price' => 1200, 'discount' => 0],
                        ['name' => 'Premium', 'price' => 1800, 'discount' => 200],
                    ],
                ],
                [
                    'name' => 'Yak Wool Socks',
                    'slug' => 'yak-wool-socks',
                    'description' => 'Thick, breathable yak wool socks for trekking and winter.',
                    'variants' => [
                        ['name' => 'Pack of 2', 'price' => 1100, 'discount' => 100],
                        ['name' => 'Pack of 4', 'price' => 2000, 'discount' => 300],
                    ],
                ],
            ],

            'bikash@technepal.test' => [
                [
                    'name' => 'Wireless Headphones',
                    'slug' => 'wireless-headphones',
                    'description' => 'Bluetooth over-ear headphones with deep bass and 30-hour battery.',
                    'variants' => [
                        ['name' => 'Black', 'price' => 4000, 'discount' => 800],
                        ['name' => 'White', 'price' => 4000, 'discount' => 800],
                    ],
                ],
                [
                    'name' => 'Smart Fitness Watch',
                    'slug' => 'smart-fitness-watch',
                    'description' => 'Heart-rate, step tracking and notifications with a bright AMOLED display.',
                    'variants' => [
                        ['name' => 'Standard',  'price' => 8000, 'discount' => 1500],
                        ['name' => 'Steel Strap', 'price' => 9500, 'discount' => 1500],
                    ],
                ],
                [
                    'name' => 'Portable Power Bank 20000mAh',
                    'slug' => 'portable-power-bank-20000mah',
                    'description' => 'Fast-charging power bank with dual USB and USB-C output.',
                    'variants' => [
                        ['name' => '20W',  'price' => 2800, 'discount' => 300],
                        ['name' => '45W',  'price' => 3800, 'discount' => 400],
                    ],
                ],
                [
                    'name' => 'Bluetooth Speaker',
                    'slug' => 'bluetooth-speaker',
                    'description' => 'Compact water-resistant speaker with 12 hours of playtime.',
                    'variants' => [
                        ['name' => 'Blue', 'price' => 3200, 'discount' => 500],
                        ['name' => 'Red',  'price' => 3200, 'discount' => 500],
                        ['name' => 'Gray', 'price' => 3200, 'discount' => 0],
                    ],
                ],
            ],
        ];

        foreach ($catalog as $merchantEmail => $products) {
            $merchant = Merchant::where('email', $merchantEmail)->firstOrFail();

            foreach ($products as $item) {
                $product = Product::updateOrCreate(
                    ['slug' => $item['slug']],
                    [
                        'name'        => $item['name'],
                        'description' => $item['description'],
                        'merchant_id' => $merchant->id,
                    ]
                );

                foreach ($item['variants'] as $variant) {
                    $product->product_varients()->updateOrCreate(
                        ['name' => $variant['name']],
                        [
                            'price'    => $variant['price'],
                            'discount' => $variant['discount'],
                        ]
                    );
                }
            }
        }
    }
}
