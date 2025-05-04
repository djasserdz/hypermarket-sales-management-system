<?php

namespace App\Http\Controllers;

use App\Http\Resources\CashierResource;
use App\Models\cashRegister;
use App\Models\cashRegister as ModelsCashRegister;
use App\Models\supermarket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Symfony\Component\HttpFoundation\Response;
use App\Models\shift;
use Carbon\Carbon;
class ManagerController extends Controller
{
    /**
     * Add new cash register
     * POST /user/addCacheRegister
     */
    public function AddCacheRegister(Request $request)
    {
        $validator = Validator::make($request->only('supermarket_id'), [
            'supermarket_id' => 'required|exists:supermarkets,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $cashRegister = cashRegister::create([
            'supermarket_id' => $request->supermarket_id
        ]);

        return response()->json([
            'message' => 'Cash register added successfully',
            'data' => $cashRegister
        ],Response::HTTP_CREATED);
    }

    /**
     * Add new cashier and assign to cash register
     * POST /user/addCachier
     */
    public function AddCachier(Request $request)
    {
        $validator = Validator::make($request->only('name','email','password'), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => ['required','string',Password::min(8)->letters()->mixedCase()->numbers()->symbols()],        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ],Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $cashier = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'cashier'
        ]);


        return response()->json([
            'message' => 'Cashier added successfully',
        ],Response::HTTP_OK);
    }

    /**
     * Get all cashiers with their cash registers
     * GET /user/cashiers
     */
    public function showAllCashiers(Request $request)
    {
        $id=$request->user()->id;
        
        $supermarket=supermarket::whereManagerId($id)->first();
        $supermarket_id=$supermarket->id;

        $cashiers = User::where('role', 'cashier')
                    ->whereHas('cashRegister', function($query) use ($supermarket_id) {
                        $query->where('supermarket_id',$supermarket_id);
                    })
                    ->with('cashRegister')
                    ->get();

        return response()->json([
            "Cashiers"=>CashierResource::collection($cashiers),
        ],Response::HTTP_OK);
    }

    /**
     * Delete cashier
     * DELETE /user/cashiers/{id}
     */
    public function deleteCashier($id)
    {
        $cashier = User::where('role', 'cashier')->find($id);

        if (!$cashier) {
            return response()->json([
                'message' => 'Cashier not found'
            ], Response::HTTP_NOT_FOUND);
        }

        // Remove from shifts table first
        $cashier->cashRegisters()->detach();
        $cashier->delete();

        return response()->json([
            'message' => 'Cashier deleted successfully'
        ],Response::HTTP_OK);
    }

    /**
     * Update cashier information
     * PUT /user/cashiers/{id}
     */
    public function editCashier(Request $request, $id)
    {
        $cashier = User::where('role', 'cashier')->find($id);

        if (!$cashier) {
            return response()->json([
                'status' => false,
                'message' => 'Cashier not found'
            ], 404);
        }

        $validator = Validator::make($request->only('name','email','password'), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => ['required','string',Password::min(8)->letters()->mixedCase()->numbers()->symbols()],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $updateData = $request->only(['name', 'email','password']);
        
        if ($request->has('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $cashier->update($updateData);

    
        return response()->json([
            'message' => 'Cashier updated successfully',
        ],Response::HTTP_OK);
    }
     public function showAllCacheRegisters(Request $request)
    {
       $managerId = $request->user()->id;

    $supermarket = supermarket::whereManagerId($managerId);
    $supermarket_id = $supermarket->id;

    $cashRegisters = cashRegister::where('supermarket_id', $supermarket_id)
                        ->get();

    return response()->json([
        'cashRegisters' => CashRegisterResource::collection($cashRegisters),
    ], Response::HTTP_OK);
}
     public function deleteCacheRegister($id){
        $cashRegister = cashRegister::find($id);

    if (!$cashRegister) {
        return response()->json([
            'message' => 'Cash register not found'
        ], Response::HTTP_NOT_FOUND);
    }

    $cashRegister->users()->detach();
    $cashRegister->delete();
    return response()->json([
        'message' => 'Cash register deleted successfully'
    ], Response::HTTP_OK);
}
public function showAllShifts(Request $request)
{
    $managerId = $request->user()->id;

    $supermarket = Supermarket::where('manager_id', $managerId)->firstOrFail();

    $cashRegisterIds = CashRegister::where('supermarket_id', $supermarket->id)->pluck('id');

    $shifts = Shift::whereIn('cash_register_id', $cashRegisterIds)
        ->whereBetween('created_at', [Carbon::today(), Carbon::tomorrow()])
        ->with(['user', 'cashRegister'])
        ->get()
        ->map(function ($shift) {
            return [
                'cash_register_id' => $shift->cash_register_id,
                'cashier_name'     => $shift->user->name,
                'start_at'         => $shift->created_at,
                'end_at'           => $shift->end_at,
            ];
        });

    return response()->json([
        'shifts' => $shifts
    ], Response::HTTP_OK);
}

    }
























/*----OLD VRS----
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
*/
