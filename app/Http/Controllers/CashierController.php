<?php

namespace App\Http\Controllers;

use App\Filament\Resources\ProductResource;
use App\Http\Resources\product as ResourcesProduct;
use App\Http\Resources\ProductResource as ResourcesProductResource;
use App\Models\product;
use App\Services\ShortestPathService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class CashierController extends Controller
{
    public function search(Request $request)
    {
        $validated = Validator::make($request->only('search'), [
            'search' => ['required', 'string'],
        ]);

        if ($validated->fails()) {
            return response()->json([
                'error' => $validated->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $search = $request->search;

   
        $cacheKey = "product_search:" . md5($search);

           
        $product = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($search) {
                return product::where('name', 'LIKE', "%$search%")
                    ->orWhere('barcode', 'LIKE', "%$search%")
                    ->first();
        });

        if (!$product) {
            return response()->json([
                'error' => 'Product not found',
            ], 404);
        }

        return response()->json([
            'product' => new ResourcesProductResource($product)
        ]);
    }

    public function generate_ticket(Request $request)
{
    $validated = Validator::make($request->only('payment_method','products'), [
        'payment_method' => ['required'],
        'products' => ['required', 'array'],
        'products.*.id' => ['required', 'exists:products,id'],
        'products.*.quantity' => ['required', 'integer', 'min:1'],
    ]);

    if ($validated->fails()) {
        return response()->json([
            'error' => $validated->errors(),
        ], Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    $user = $request->user();
    $cashRegister = $user->cashRegister()->first();

    if (!$cashRegister) {
        return response()->json([
            'error' => 'You are not assigned to a cash register. Please login.',
        ], Response::HTTP_FORBIDDEN);
    }

    $supermarket_id = $cashRegister->supermarket_id;
    $productIDs = collect($request->products)->pluck('id');

    $products = product::with(['supermarket' => function ($query) use ($supermarket_id) {
        $query->where('supermarket_id', $supermarket_id);
    }])->whereIn('id', $productIDs)->get()->keyBy('id');

    $productData = [];
    $total = 0;
    $productTransfers = []; // Track which products need transfers

    DB::beginTransaction();

    try {
        $sale = $cashRegister->sales()->create([
            'payment_method' => $request->payment_method,
        ]);

        foreach ($request->products as $item) {
            $product = $products[$item['id']] ?? null;

            if (!$product) {
                throw new \Exception("Product with ID {$item['id']} not found.");
            }

            $stock = $product->supermarket->first()?->pivot;

            // Check if stock is low (less than 30) or insufficient for the sale
            if (!$stock || $stock->quantity < $item['quantity']) {
                throw new \Exception("Not enough quantity for product {$product->name}.");
            }
            
            // If stock will be below threshold after this sale, track for transfer
            if ($stock->quantity - $item['quantity'] < 30) {
                $productTransfers[] = [
                    'product_id' => $product->id,
                    'quantity_needed' => 100 // Requesting standard restock amount
                ];
            }

            $stock->decrement('quantity', $item['quantity']);

            $sale->products()->attach($product->id, [
                'quantity' => $item['quantity'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $subtotal = $product->price * $item['quantity'];
            $total += $subtotal;

            $productData[] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => $item['quantity'],
                'subtotal' => round($subtotal, 2),
            ];
        }

        // Create transfer requests for products with low stock
        $service = new ShortestPathService();
        $transferResults = [];
        
        foreach ($productTransfers as $transfer) {
            try {
                $result = $service->calculateAndCreateTransfer(
                    $supermarket_id,
                    $transfer['product_id'],
                    $transfer['quantity_needed']
                );
                $transferResults[] = $result;
            } catch (\Exception $e) {
                // Log transfer creation failure but don't halt the sale
                Log::warning("Failed to create transfer for product ID {$transfer['product_id']}: " . $e->getMessage());
            }
        }

        DB::commit();

        $ticket = [
            'sale_id' => $sale->id,
            'supermarket_name' => $cashRegister->supermarket->first()?->name,
            'date' => now()->toDateTimeString(),
            'payment_method' => $sale->payment_method,
            'cash_register_id' => $cashRegister->id,
            'cashier' => $user->name,
            'products' => $productData,
            'total' => round($total, 2),
        ];

        return response()->json([
            'ticket' => $ticket,
            'transfers_initiated' => count($transferResults) > 0 ? $transferResults : null
        ]);
    } catch (\Exception $e) {
        DB::rollBack();

        return response()->json([
            'error' => $e->getMessage(),
        ], 500);
    }
}
        /*$stocks = stock::with('product', 'supermarket')->where('supermarket_id', $supermarket_id)->whereIn('product_id', $productIDS)->get();
        $stockData = [];

        foreach ($stocks as $stock) {
            $stockData[] = [
                'product_id' => $stock->product->id,
                'product_name' => $stock->product->name,
                'product_price' => $stock->product->price,
                'stock_quantity' => $stock->quantity,
                'supermarket_name' => $stock->supermarket->name,
            ];
        }


        return response()->json([
            'stocks' => $stockData
        ]);*/
    }



