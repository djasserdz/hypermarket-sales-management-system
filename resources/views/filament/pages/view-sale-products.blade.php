<x-filament::page>
    <div class="space-y-6">
        <!-- Header section with sale information -->
        <div class="p-6 bg-gray-900 rounded-xl shadow">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold tracking-tight text-white">
                    Sale #{{ $sale->id }} Details
                </h1>
                <span class="px-3 py-1 text-xs font-medium rounded-full bg-amber-100/10 text-amber-400">
                    {{ $sale->created_at->format('F j, Y h:i A') }}
                </span>
            </div>
            
            <div class="grid grid-cols-1 gap-4 mt-4 md:grid-cols-3">
                <div class="p-4 border rounded-lg border-gray-700 bg-gray-800">
                    <span class="text-sm font-medium text-gray-400">Sold By</span>
                    <p class="mt-1 text-gray-300">{{ optional($sale->cashierAtTimeOfSale())->name ?? '—' }}</p>
                </div>
                <div class="p-4 border rounded-lg border-gray-700 bg-gray-800">
                    <span class="text-sm font-medium text-gray-400">Cash Register</span>
                    <p class="mt-1 text-gray-300">{{ $sale->cashRegister->id ?? '—' }}</p>
                </div>
                <div class="p-4 border rounded-lg border-gray-700 bg-gray-800">
                    <span class="text-sm font-medium text-gray-400">Total Items</span>
                    <p class="mt-1 text-gray-300">{{ $sale->products->sum('pivot.quantity') ?? 0 }}</p>
                </div>
            </div>
        </div>
        
        <!-- Products table with proper styling -->
        <div class="overflow-hidden bg-gray-900 shadow-sm rounded-xl">
            <div class="p-4 mb-2 border-b border-gray-700">
                <h2 class="text-lg font-bold tracking-tight text-white">Products in this Sale</h2>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-800">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium text-gray-400">Product Name</th>
                            <th class="px-4 py-3 text-center font-medium text-gray-400">Quantity</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-400">Price</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-400">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($sale->products as $product)
                            <tr class="border-t border-gray-700">
                                <td class="px-4 py-3 text-gray-300">{{ $product->name }}</td>
                                <td class="px-4 py-3 text-center text-gray-300">{{ $product->pivot->quantity }}</td>
                                <td class="px-4 py-3 text-right text-gray-300">DZD{{ number_format($product->price, 2) }}</td>
                                <td class="px-4 py-3 font-medium text-right text-gray-300">DZD{{ number_format($product->price * $product->pivot->quantity, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-3 text-center text-gray-500">No products found in this sale</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-white/5">
                        <tr>
                            <td colspan="3" class="px-4 py-3 text-right font-medium text-gray-300">Total:</td>
                            <td class="px-4 py-3 text-right font-bold text-amber-400">
                                DZD{{ number_format($sale->products->sum(function($product) {
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