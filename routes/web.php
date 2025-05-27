<?php

use App\Filament\Resources\ProductResource;
use App\Http\Resources\ProductResource as ResourcesProductResource;
use App\Models\product;
use App\Models\User;
use Filament\Notifications\Notification; // Ensure the Filament Notifications package is installed
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;


Route::redirect("/","/admin/login");


