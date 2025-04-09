<x-filament::widget>
    <x-filament::card>
        <h2 class="text-lg font-bold mb-2">Top Selling Products</h2>
        <ul class="space-y-2">
            @foreach ($topProducts as $product)
                <li class="flex justify-between p-2 border-b">
                    <span>{{ $product->name }}</span>
                    <span class="font-bold">{{ $product->total_sold }}</span>
                </li>
            @endforeach
        </ul>
    </x-filament::card>
</x-filament::widget>
