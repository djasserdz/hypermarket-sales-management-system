<?php

namespace App\Http\Controllers;

use App\Models\cashRegister;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validation = Validator::make($request->only(['password', 'cash_register_id', 'email']), [
            'email' => ['required', 'email'],
            'password' => ['required'],
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
        $cash_register = cashRegister::find($request->cash_register_id);
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

    public function logout(Request $request)
    {
        $user = $request->user();
        $latestshift = $user->cashRegister()->withPivot('start_at', 'end_at')->orderByDesc('shifts.start_at')->first();

        if ($latestshift) {
            $user->cashRegister()->updateExistingPivot($latestshift->id, [
                'end_at' => now(),
            ]);
        }

        $user->tokens()->delete();

        return response()->json([
            'message' => 'User logged out!',
        ]);
    }
}
