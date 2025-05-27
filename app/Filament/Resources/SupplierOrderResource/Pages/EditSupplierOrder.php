<?php

namespace App\Filament\Resources\SupplierOrderResource\Pages;

use App\Filament\Resources\SupplierOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSupplierOrder extends EditRecord
{
    protected static string $resource = SupplierOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    // If you need to disable editing of supplier_id on the edit page, 
    // you can modify the form schema in the resource itself or here.
    // For example, by overriding the getFormSchema method if it was more complex.
    // However, since supplier_id is already disabled in the main resource form, 
    // it should carry over to the edit page as well.
} 