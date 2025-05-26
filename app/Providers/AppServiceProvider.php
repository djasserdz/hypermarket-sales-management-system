<?php

namespace App\Providers;


use Illuminate\Support\Facades\URL;
use App\Http\Resources\User;
use App\Models\cashRegister;
use App\Policies\managerPolicy;
use App\Policies\UserPolicy;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Notification::make()
            ->title('Filament Notifications Active')
            ->success()
            ->send();
        Gate::policy(cashRegister::class,managerPolicy::class);
        Gate::policy(User::class,UserPolicy::class);

        if (isset($_SERVER['HTTP_HOST'])) {
        $scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
        URL::forceRootUrl($scheme . $_SERVER['HTTP_HOST']);

        if ($scheme === 'https://') {
            URL::forceScheme('https');
        }
    }
    }
}
