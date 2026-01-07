<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use App\Filament\Admin\Pages\Dashboard;
use Illuminate\Support\HtmlString;
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
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->authGuard('web')
            ->colors([
                'danger' => Color::Rose,
                'gray' => Color::Slate,
                'info' => Color::Blue,
                'primary' => Color::Green, // Dark green
                'success' => Color::Emerald,
                'warning' => Color::Orange,
            ])
            ->font('Segoe UI Variable')
            ->brandLogo(fn () => new HtmlString('<img src="' . asset('images/psgears.png') . '" alt="Paulinian E-Portfolio" style="height: 40px;" />'))
            ->darkModeBrandLogo(fn () => new HtmlString('<img src="' . asset('images/psgears.png') . '" alt="Paulinian E-Portfolio" style="height:40px;" />'))
            ->brandLogoHeight('36px')
            ->breadcrumbs(false)
            ->discoverResources(in: app_path('Filament/Admin/Resources'), for: 'App\Filament\Admin\Resources')
            ->discoverPages(in: app_path('Filament/Admin/Pages'), for: 'App\Filament\Admin\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->navigationGroups([
                NavigationGroup::make()
                    ->label('Admin Settings')
                    ->collapsible(),
            ])
            ->collapsibleNavigationGroups(false)

            ->widgets([
                \App\Filament\Admin\Widgets\AdminStatsOverviewWidget::class,
                \App\Filament\Admin\Widgets\LatestOrganizationsWidget::class,
                \App\Filament\Admin\Widgets\RanksChartWidget::class,
                // Register the ranks stats widget so Livewire/Filament can mount it when used in pages
                \App\Filament\Admin\Widgets\RanksStatsWidget::class,
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
