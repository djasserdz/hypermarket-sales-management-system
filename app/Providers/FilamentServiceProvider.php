<?php

namespace App\Providers;

use Filament\Facades\Filament;
use App\Models\User;
use Illuminate\Support\ServiceProvider;

class FilamentServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Filament::serving(function () {
            Filament::auth(function (User $user) {
                // Allow users with the 'admin' role to access Filament
                return $user->role() === 'admin'; // Or use other roles like 'manager' based on your needs
            });
        });
    }
}
