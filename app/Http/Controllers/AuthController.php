<?php

namespace App\Http\Controllers;

use App\Models\cashRegister;
use App\Models\User;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validation = Validator::make($request->only(['user_id', 'cash_register_id']), [
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['required', 'string'],
            'cash_register_id' => ['required'],
        ]);

        if ($validation->fails()) {
            return response()->json([
                'errors' => $validation->errors(),
            ]);
        }
        $user = User::where('email', $request->input('email'))->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['error' => 'Credientals does not match our records']);
        }
        $cash_register = cashRegister::find($request->input('cash_register'));
        if (!$cash_register) {
            return response()->json(['error' => 'cash register not found']);
        }




        $token = $user->createToken($user->id)->plainTextToken;


        $user->cashRegister()->attach($request->input('cash_register_id'), ["start_at" => now()]);

        return response()->json([
            'message' => 'User logged in!',
            'token' => $token,
        ]);
    }
}
