<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CashierController;
use App\Http\Controllers\ManagerController;
use App\Http\Middleware\Authenticationmiddleware;
use App\Http\Middleware\IsManagermiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware(Authenticationmiddleware::class);

Route::post('/search', [CashierController::class, 'search']);

Route::post('/ticket', [CashierController::class, 'generate_ticket'])->middleware(Authenticationmiddleware::class);
Route::post('/user/addCacheRegister',[ManagerController::class,'AddCacheRegister'])->middleware(IsManagermiddleware::class);
Route::post('/user/addCachier',[ManagerController::class,'AddCachier'])->middleware(IsManagermiddleware::class);
