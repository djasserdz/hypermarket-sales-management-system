<?php

use App\Models\User;
use Filament\Notifications\Notification; // Ensure the Filament Notifications package is installed
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/not', function () {
    $user = User::find(1); // Target User

    Notification::make()
        ->title('New Message')
        ->body('You have a new notification.')
        ->sendToDatabase($user);
    dd('send');
});
