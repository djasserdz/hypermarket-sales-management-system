<?php

namespace App\Providers\Filament;

use App\Filament\Resources\ProductResource\Widgets\TopSellingProducts;
use App\Filament\Resources\UserResource;
use App\Filament\Widgets\SalesSummary;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Pages;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Filament\Widgets\Widget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use SaleChart;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->brandName('Hypermarket Sales')
            ->brandLogo(asset('logo/project-newLogo.png'))
            ->brandLogoHeight('50px')
            ->darkMode('true')
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->databaseNotifications()->databaseNotificationsPolling('2s')
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                SalesSummary::class,
                TopSellingProducts::class,
                SaleChart::class,

            ])
            ->navigationGroups([
                NavigationGroup::make()->label('Users Management'),
                NavigationGroup::make()->label('Supermarkets'),
                NavigationGroup::make()->label('Sales Management'),
                NavigationGroup::make()->label("Stock Management"),
                NavigationGroup::make()->label('Settings')->icon('heroicon-o-cog')
            ])
            ->navigationItems([
                //NavigationItem::make('Dashboard')->icon('heroicon-o-home')->url('/admin/dashboard'),
                NavigationItem::make('Sales')->icon('heroicon-o-shopping-cart')->url('/admin/sales')->group('Sales Management'),
                NavigationItem::make('Products')->icon('heroicon-o-cube')->url('/admin/products')->group('Stock Management'),
                NavigationItem::make('Categories')->icon('heroicon-o-queue-list')->url('/admin/categories')->group('Stock Management'),
                NavigationItem::make('Suppliers')->icon('heroicon-o-truck')->url('/admin/suppliers')->group('Stock Management'),
                NavigationItem::make('Stocks')->icon('heroicon-o-archive-box')->url('/admin/stocks')->group('Stock Management'),
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
