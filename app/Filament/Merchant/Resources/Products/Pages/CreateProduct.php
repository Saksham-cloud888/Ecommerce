<?php

namespace App\Filament\Merchant\Resources\Products\Pages;

use App\Filament\Merchant\Resources\Products\ProductResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['merchant_id'] = Auth::guard('merchant')->id();

        return $data;
    }
}
