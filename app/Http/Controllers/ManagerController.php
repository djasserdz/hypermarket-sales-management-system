<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\cashRegister;
use Illuminate\Support\Facades\Validator;


class ManagerController extends Controller
{
  public function AddCacheRegister(Request $request){
    $validation = Validator::make($request->only('supermarket_id'), [
        'supermarket_id' => ['required', 'exists:supermarkets,id'],
    ]);

    if ($validation->fails()) {
        return response()->json([
            'message' => $validation->errors(),
        ]);
    }

    $supermarket_id = $request->supermarket_id;
    cashRegister::create(["supermarket_id"=>$supermarket_id]);
    return response()->json(['Message'=>"cache register added successfully!!"]);
  }
  public function AddCachier(Request $request){
    $validation = Validator::make($request->only(   'name',
        'email',
        'password'), [
        'name' => ['required', 'string'],
        'email'=>['required','email'],
        'password'=>['required','string']
    ]);

    if ($validation->fails()) {
        return response()->json([
            'message' => $validation->errors(),
        ]);
    }

    $name = $request->name;
    $email=$request->email;
    $password=$request->paswword;
    $role="cachier";
    cashRegister::create([
        "name"=>$name,
        "email"=>$email,
        "password"=>$password,
        "role"=>$role

]);
    return response()->json(['Message'=>"cacheier added successfully!!"]);
  }


}
