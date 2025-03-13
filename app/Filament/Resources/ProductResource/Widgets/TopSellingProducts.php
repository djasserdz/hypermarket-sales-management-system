<?php

namespace App\Filament\Resources\ProductResource\Widgets;

use App\Models\product;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;

class TopSellingProducts extends Widget
{
    protected static string $view = 'filament.resources.product-resource.widgets.top-selling-products';

    protected function getViewData(): array
    {
        $topProducts = product::select('products.name', DB::raw('SUM(sale_items.quantity) as total_sold'))
            ->join('sale_items', 'products.id', '=', 'sale_items.product_id')
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_sold')
            ->limit(5)->get();

        return compact('topProducts');
    }
}
