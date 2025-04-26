<?php

namespace App\Http\Controllers;

use App\Filament\Resources\UserResource;
use App\Http\Resources\User as ResourcesUser;
use App\Models\cashRegister;
use App\Models\shift;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Filament\Facades\Filament;
class AuthController extends Controller
{
    public function login(Request $request)
{
    $validation = Validator::make($request->only(['password', 'cash_register_id', 'name']), [
        'name' => ['required', 'string'],
        'password' => ['required'],
        'cash_register_id' => ['nullable'],
    ]);

    if ($validation->fails()) {
        return response()->json([
            'errors' => $validation->errors(),
        ],Response::HTTP_BAD_REQUEST);
    }
    $user = User::where('name', $request->input('name'))->first();
    

    
    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json(['error' => 'Credentials do not match our records'],Response::HTTP_UNAUTHORIZED);
    }

    if($user->role() == 'cashier'){
        $cash_register = cashRegister::find($request->cash_register_id);
        if (!$cash_register) {
            return response()->json(['error' => 'Cash register not found']);
        }
        
       
        $existingShift = $cash_register->users()->whereNull('end_at')->first();
        if ($existingShift) {
            return response()->json(['error' => 'This cash register is already in use'],Response::HTTP_UNAUTHORIZED);
        }
    
        $user->cashRegister()->attach($request->input('cash_register_id'), ['start_at' => now()]);
        
        
    }
    
    $token = $user->createToken($user->id)->plainTextToken;

    return response()->json([
        'message' => 'User logged in!',
        'token' => $token,
        'role'=>$user->role(),
    ]);
}


public function logout(Request $request) {
    $user = $request->user();
         
    $latestShift=shift::where('user_id',$user->id)->where('end_at',null)->first();

    if ($latestShift) {
      DB::table('shifts')
      ->where('id',$latestShift->id)
      ->limit(1)
      ->update(['end_at'=>now()]);
    }
    
     $user->tokens()->delete();
    
    return response()->json([
        'message' => 'User logged out!',
    ]);
}
 public function get_user(Request $request){
    $user=$request->user();
    if(!$user){
        return response()->json([
            'error'=>"User not found!"
        ],Response::HTTP_NOT_FOUND);
    }

    return response()->json([
        'user'=>new ResourcesUser($user)
    ],Response::HTTP_OK);
 }


}
