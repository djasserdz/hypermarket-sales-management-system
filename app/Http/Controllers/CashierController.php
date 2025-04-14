<?php

namespace App\Http\Controllers;

use App\Http\Resources\product as ResourcesProduct;
use App\Models\product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

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
            ], 422);
        }

        $search = $request->search;

        $product = product::where('name', 'LIKE', "%$search%")
            ->orWhere('barcode', 'LIKE', "%$search%")
            ->first();

        if (!$product) {
            return response()->json([
                'error' => 'Product not found',
            ], 404);
        }

        return response()->json([
            'product' => new ResourcesProduct($product),
        ]);
    }

    public function generate_ticket(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'payment_method' => ['required'],
            'products' => ['required', 'array'],
            'products.*.id' => ['required', 'exists:products,id'],
            'products.*.quantity' => ['required', 'integer', 'min:1'],
        ]);

        if ($validated->fails()) {
            return response()->json([
                'error' => $validated->errors(),
            ], 422);
        }

        $user = $request->user();
        $cashRegister = $user->cashRegister()->first();

        if (!$cashRegister) {
            return response()->json([
                'error' => 'You are not assigned to a cash register. Please login.',
            ], 403);
        }

        $supermarket_id = $cashRegister->supermarket_id;
        $productIDs = collect($request->products)->pluck('id');

        $products = product::with(['supermarket' => function ($query) use ($supermarket_id) {
            $query->where('supermarket_id', $supermarket_id);
        }])->whereIn('id', $productIDs)->get()->keyBy('id');

        $productData = [];
        $total = 0;

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

                if (!$stock || $stock->quantity < $item['quantity']) {
                    throw new \Exception("Not enough quantity for product {$product->name}.");
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

            return response()->json(['ticket' => $ticket]);
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



