<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Dashboard;
use App\Http\Middleware\FilamentAdminMiddleware;
use Filament\Enums\GlobalSearchPosition;
use Filament\Enums\UserMenuPosition;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $panel = $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->brandLogo(fn() => view('filament.admin.logo'))
            ->favicon(secure_asset('favicon.ico'))
            ->login()
            ->colors([
                'primary' => Color::Gray,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
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
                FilamentAdminMiddleware::class,
            ])
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->userMenu(position: UserMenuPosition::Sidebar)
            ->collapsibleNavigationGroups(true)
            ->sidebarCollapsibleOnDesktop()
            ->globalSearch(position: GlobalSearchPosition::Sidebar)
            ->colors([
                'primary' => [
                    50 => '#f5f3ff',
                    100 => '#ede9fe',
                    200 => '#ddd6fe',
                    300 => '#c4b5fd',
                    400 => '#a78bfa',
                    500 => '#8b5cf6',
                    600 => '#7c3aed',
                    700 => '#6d28d9',
                    800 => '#5b21b6',
                    900 => '#4c1d95',
                    950 => '#3b1078',
                ]
            ]);

        $votingPluginClass = 'SilkPanel\\Voting\\VotingPlugin';
        if (class_exists($votingPluginClass)) {
            $panel->plugin($votingPluginClass::make());
        }

        return $panel;
    }
}
