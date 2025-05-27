<?php

namespace App\Filament\Resources\SupplierOrderResource\Pages;

use App\Filament\Resources\SupplierOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Product; // Import Product model

class CreateSupplierOrder extends CreateRecord
{
    protected static string $resource = SupplierOrderResource::class;

    // Optionally, you can add logic here to auto-fill supplier_id
    // based on product_id after the form is hydrated, if needed beyond the main resource form logic
    // However, the current setup in SupplierOrderResource.php handles this with reactive fields.

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    // Example of mutating data before creation if supplier_id wasn't set reactively
    // protected function mutateFormDataBeforeCreate(array $data): array
    // {
    //     if (isset($data['product_id']) && !isset($data['supplier_id'])) {
    //         $product = Product::find($data['product_id']);
    //         if ($product) {
    //             $data['supplier_id'] = $product->supplier_id;
    //         }
    //     }
    //     return $data;
    // }
} 