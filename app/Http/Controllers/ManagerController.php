<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\cashRegister;
use Illuminate\Support\Facades\Validator;

class ManagerController extends Controller
{
    public function AddCacheRegister(Request $request)
    {
        $validation = Validator::make($request->only('supermarket_id'), [
            'supermarket_id' => ['required', 'exists:supermarkets,id'],
        ]);

        if ($validation->fails()) {
            return response()->json([
                'status' => false,
                'message' => "wiiiw",
            ], 422);
        }

        $supermarket_id = $request->supermarket_id;
        cashRegister::create(["supermarket_id" => $supermarket_id]);

        return response()->json([
            'status' => true,
            'message' => "Cash register added successfully!"
        ]);
    }

    public function AddCachier(Request $request)
    {
        $validation = Validator::make($request->only('name', 'email', 'password'), [
            'name' => ['required', 'string'],
            'email' => ['required', 'email', 'unique:cash_registers,email'],
            'password' => ['required', 'string']
        ]);

        if ($validation->fails()) {
            return response()->json([
                'status' => false,
                'message' =>"if there is error is hicham mistake not main",
            ], 422);
        }

        $name = $request->name;
        $email = $request->email;
        $password = bcrypt($request->password);
        $role = "cachier";

        cashRegister::create([
            "name"     => $name,
            "email"    => $email,
            "password" => $password,
            "role"     => $role
        ]);

        return response()->json([
            'status' => true,
            'message' => "Cashier added successfully!"
        ]);
    }

    public function showAllCashiers()
    {
        $cashiers = cashRegister::where('role', 'cachier')->get();
        return response()->json([
            'status' => true,
            'data' => $cashiers
        ]);
    }

    public function deleteCashier($id)
    {
        $cashier = cashRegister::where('role', 'cachier')->find($id);

        if (!$cashier) {
            return response()->json([
                "status" => false,
                "message" => "Cashier not found"
            ], 404);
        }

        $cashier->delete();

        return response()->json([
            "status" => true,
            "message" => "Cashier deleted successfully"
        ]);
    }

    public function editCashier(Request $request, $id)
    {
        $cashier = cashRegister::where('role', 'cachier')->find($id);

        if (!$cashier) {
            return response()->json([
                "status" => false,
                "message" => "Cashier not found"
            ], 404);
        }
        if ($request->has('password')) {
            $request['password'] = bcrypt($request->password);
        }

        $cashier->update($request->all());

        return response()->json([
            "status" => true,
            "message" => "Cashier updated successfully",
            "data" => $cashier
        ]);
    }
}
