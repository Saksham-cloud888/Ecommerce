<?php

namespace Database\Seeders;

use App\Models\Merchant;
use Illuminate\Database\Seeder;

class MerchantSeeder extends Seeder
{
    public function run(): void
    {
        $merchants = [
            [
                'name'      => 'Sita Gurung',
                'email'     => 'sita@himalayanweaves.test',
                'shop_name' => 'Himalayan Weaves',
                'contact'   => '9801111111',
            ],
            [
                'name'      => 'Bikash Thapa',
                'email'     => 'bikash@technepal.test',
                'shop_name' => 'TechNepal',
                'contact'   => '9802222222',
            ],
        ];

        foreach ($merchants as $data) {
            Merchant::updateOrCreate(
                ['email' => $data['email']],
                $data + [
                    'password' => 'password', // hashed automatically by the model cast
                    'status'   => 'approve',
                ]
            );
        }
    }
}
