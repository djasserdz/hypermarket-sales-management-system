<?php

namespace App\Http\Controllers;

use App\Http\Resources\product as ResourcesProduct;
use App\Models\product;
use App\Models\sale;
use App\Models\stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class CashierController extends Controller
{
    public function search(Request $request)
    {
        $validation = Validator::make($request->only('search'), [
            'search' => ['required', 'string'],
        ]);

        if ($validation->fails()) {
            return response()->json([
                'message' => $validation->errors(),
            ]);
        }

        $search = $request->search;

        $product = product::where('name', 'LIKE', "%$search%")->orWhere("barcode", "LIKE", "%$search%")->first();

        if (!$product) {
            return response()->json([
                'error' => 'Product not found',
            ]);
        }

        return response()->json([
            new ResourcesProduct($product),
        ]);
    }

    public function generate_ticket(Request $request)
    {
        $validation = Validator::make($request->only('products', 'payment_method'), [
            'payment_method' => ['required'],
            'products' => ['required', 'array'],
            'products.*.id' => ['required', 'exists:products,id'],
            'products.*.quantity' => ['required'],

        ]);
        if ($validation->fails()) {
            return response()->json([
                'error' => $validation->errors(),
            ]);
        }

        $user = $request->user();
        $cashRegister = $user->cashRegister()->first();
        if (!$cashRegister) {
            return response()->json([
                'error' => 'You are not assign to a cash register! Please login',
            ]);
        }


        $supermarket_id = $cashRegister->supermarket_id;

        $productIDS = collect($request->products)->pluck('id');

        $products = product::with(['supermarket' => function ($query) use ($supermarket_id) {
            $query->where('supermarket_id', $supermarket_id);
        }])->whereIn('id', $productIDS)->get()->keyBy('id');


        $productData = [];

        DB::beginTransaction();

        try {

            $sale = $cashRegister->sales()->create(['payment_method' => $request->input('payment_method')]);
            $total = 0;

            foreach ($request->products as $item) {


                if (!$item['id']) {
                    return response()->json([
                        'error' => "item with ID {$item['id']} not found",
                    ]);
                }

                $product = $products[$item['id']];


                if ($product->supermarket->first()?->pivot->quantity < $item['quantity']) {
                    return response()->json([
                        'error' => 'not enough quantity'
                    ]);
                }

                $product->supermarket->first()?->pivot->decrement('quantity', $item['quantity']);

                $sale->products()->attach($product->id, ['quantity' => $item['quantity'], 'created_at' => now(), 'updated_at' => now()]);

                $subtotal = $product->price * $item['quantity'];
                $total += $subtotal;

                $productData[] = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'quantity' => $item['quantity'],
                    'subtotal' => $subtotal,
                ];
            }

            DB::commit();

            $ticket = [
                'sale_id' => $sale->id,
                'supermarket name' => $cashRegister->supermarket->first()?->name,
                'date' => $sale->created_at->toDateTimeString(),
                'payment_method' => $sale->payment_method,
                'cash_register' => $cashRegister->id,
                'cashier' => $request->user()->name,
                'product' => $productData,
                'total' => round($total, 2),
            ];

            return response()->json([
                $ticket
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => $e,
            ]);
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
}
