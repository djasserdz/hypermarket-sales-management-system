<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CashierController;
use App\Http\Controllers\ManagerController;
use App\Http\Middleware\Authenticationmiddleware;
use App\Http\Middleware\IsCashierMiddleware;
use App\Http\Middleware\IsManagermiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'login']);

Route::middleware(Authenticationmiddleware::class)->group(function(){

    Route::get('/user',[AuthController::class,'get_user']);

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::middleware(IsCashierMiddleware::class)->group(function(){
        
        Route::post('/search', [CashierController::class, 'search']);
        Route::post('/ticket', [CashierController::class, 'generate_ticket']);
    });
    
    
    Route::middleware(IsManagermiddleware::class)->group(function(){
        Route::post('/user/addCacheRegister', [ManagerController::class, 'AddCacheRegister']);
        Route::post('/user/addCachier', [ManagerController::class, 'AddCachier']);
        Route::get('/user/cashiers', [ManagerController::class, 'showAllCashiers']);
         Route::get('/user/cacheRegisters', [ManagerController::class, 'showAllCacheRegister']);
         Route::delete('/user/cacheRegisters/{id}', [ManagerController::class, 'deleteCacheRegister']);
        Route::delete('/user/cashiers/{id}', [ManagerController::class, 'deleteCashier']);
        Route::put('/user/cashiers/{id}', [ManagerController::class, 'editCashier']);
    });
});


