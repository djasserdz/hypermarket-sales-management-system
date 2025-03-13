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
        $validation = Validator::make($request->only('products'), [
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

        foreach ($products as $product) {
            $productData[] = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'product_price' => $product->price,
                'stock_quantity' => $product->supermarket->first()?->pivot->quantity,
            ];
        }

        return response()->json([
            'stock' => $productData
        ]);

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
