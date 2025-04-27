<?php

namespace App\Filament\Resources\SalesResource\Pages;

use App\Filament\Resources\SalesResource;
use App\Models\sale;
use Filament\Resources\Pages\Page;

class ViewSaleProducts extends Page
{
    public sale $sale; // Sale model instance with related products
    
    protected static string $resource = SalesResource::class;
    
    // Define the view for the page
    protected static string $view = 'filament.pages.view-sale-products';
    
    // Add this static method to define the route
    public static function route(string $path): \Filament\Resources\Pages\PageRegistration
    {
        return parent::route($path);
    }
    
    // Update the mount method to use record instead of saleId
    public function mount(Sale $record)
    {
        $this->sale = $record;
        $this->sale->load('products');
    }
    
    // Title for the page
    protected static ?string $title = 'View Products in Sale';
}