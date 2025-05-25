<?php

namespace App\Http\Controllers;

use App\Http\Resources\CashierResource;
use App\Http\Resources\CashRegisterResource;
use App\Models\CashRegister;
use App\Models\CashRegister as ModelsCashRegister;
use App\Models\Supermarket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Shift;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ManagerController extends Controller
{

    use AuthorizesRequests;
    /**
     * Add new cash register
     * POST /user/addCacheRegister
     */
    public function AddCashRegister(Request $request)
    {
        $user_id=$request->user()->id;

        $supermarket_id=Supermarket::whereManagerId($user_id)->value("id");

        
        $cashRegister = CashRegister::create([
            'supermarket_id' => $supermarket_id,
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
    public function AddCashier(Request $request)
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
        
        $supermarket_id=Supermarket::whereManagerId($id)->value("id");
        
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
        
        $cashier = User::find($id);
        
        if (!$cashier) {
            return response()->json([
                'message' => 'Cashier not found'
            ], Response::HTTP_NOT_FOUND);
        }

        

        
        $cashier->cashRegister()->detach();
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
        $cashier = User::find($id);

        

        if (!$cashier) {
            return response()->json([
                'message' => 'Cashier not found'
            ], Response::HTTP_NOT_FOUND);
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
     public function showAllCashRegisters(Request $request)
    {
       $managerId = $request->user()->id;

       $supermarket_id = Supermarket::whereManagerId($managerId)->value("id");
      

       $cashRegisters = CashRegister::where('supermarket_id', $supermarket_id)
                        ->get();


    return response()->json([
        'cashRegisters' => CashRegisterResource::collection($cashRegisters),
    ], Response::HTTP_OK);
}
     public function deleteCashRegister($id){
        $cashRegister = CashRegister::find($id);

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

    $supermarket_id = Supermarket::where('manager_id', $managerId)->value('id');

    $cashRegisterIds = CashRegister::where('supermarket_id', $supermarket_id)->pluck('id');

    $shifts = Shift::whereIn('cash_register_id', $cashRegisterIds)
        ->whereBetween('start_at', [Carbon::today(), Carbon::tomorrow()])
        ->with(['user', 'cashRegister'])
        ->get()
        ->map(function ($shift) {
            return [
                'cash_register_id' => $shift->cash_register_id,
                'cashier_name'     => $shift->user->name,
                'start_at'         => $shift->start_at,
                'end_at'           => $shift->end_at,
            ];
        });

    return response()->json([
        'shifts' => $shifts
    ], Response::HTTP_OK);
}
}



