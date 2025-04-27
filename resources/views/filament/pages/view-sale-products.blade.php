<x-filament::page>
    <div class="space-y-6">
        <!-- Header section with sale information -->
        <div class="p-6 bg-white rounded-xl shadow">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold tracking-tight text-gray-950">
                    Sale #{{ $sale->id }} Details
                </h1>
                <span class="px-3 py-1 text-xs font-medium rounded-full bg-primary-50 text-primary-600">
                    {{ $sale->created_at->format('F j, Y g:i A') }}
                </span>
            </div>
            
            <div class="grid grid-cols-1 gap-4 mt-4 md:grid-cols-3">
                <div class="p-3 border rounded-lg border-gray-200">
                    <span class="text-sm font-medium text-gray-500">Sold By</span>
                    <p class="mt-1 text-sm">{{ optional($sale->cashierAtTimeOfSale())->name ?? '—' }}</p>
                </div>
                <div class="p-3 border rounded-lg border-gray-200">
                    <span class="text-sm font-medium text-gray-500">Cash Register</span>
                    <p class="mt-1 text-sm">{{ $sale->cashRegister->id ?? '—' }}</p>
                </div>
                <div class="p-3 border rounded-lg border-gray-200">
                    <span class="text-sm font-medium text-gray-500">Total Items</span>
                    <p class="mt-1 text-sm">{{ $sale->products->sum('pivot.quantity') ?? 0 }}</p>
                </div>
            </div>
        </div>
        
        <!-- Products table with proper styling -->
        <div class="overflow-hidden bg-white shadow-sm ring-1 ring-gray-950/5 rounded-xl">
            <div class="p-4 mb-2 border-b">
                <h2 class="text-lg font-bold tracking-tight text-gray-950">Products in this Sale</h2>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-sm divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium text-gray-500">Product Name</th>
                            <th class="px-4 py-3 text-center font-medium text-gray-500">Quantity</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-500">Price</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-500">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse ($sale->products as $product)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">{{ $product->name }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center justify-center h-6 px-2 text-sm font-medium rounded-full bg-primary-50 text-primary-700">
                                        {{ $product->pivot->quantity }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">${{ number_format($product->price, 2) }}</td>
                                <td class="px-4 py-3 font-medium text-right">${{ number_format($product->price * $product->pivot->quantity, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-3 text-center text-gray-500">No products found in this sale</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="3" class="px-4 py-3 text-right font-medium">Total:</td>
                            <td class="px-4 py-3 text-right font-bold text-primary-600">
                                ${{ number_format($sale->products->sum(function($product) {
                                    return $product->price * $product->pivot->quantity;
                                }), 2) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        
        <!-- Action buttons -->
        <div class="flex justify-between">
            <x-filament::button 
                icon="heroicon-o-arrow-left"
                color="gray"
                tag="a"
                href="{{ \App\Filament\Resources\SalesResource::getUrl('index') }}"
            >
                Back to Sales
            </x-filament::button>
        </div>
    </div>
</x-filament::page>