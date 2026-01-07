<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Support\HtmlString;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class StudentPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('student')
            ->path('student')
            ->login()
            ->authGuard('student')
            ->authPasswordBroker('students')
            ->colors([
                'danger' => Color::Rose,
                'gray' => Color::Slate,
                'info' => Color::Blue,
                'primary' => Color::Hex('#006400'), // Dark green
                'success' => Color::Emerald,
                'warning' => Color::Orange,
            ])
            ->font('Segoe UI Variable')
            ->brandLogo(fn () => new HtmlString('<img src="' . asset('images/psgears.png') . '" alt="Paulinian E-Portfolio" style="height: 40px;" />'))
            ->darkModeBrandLogo(fn () => new HtmlString('<img src="' . asset('images/psgears.png') . '" alt="Paulinian E-Portfolio" style="height:40px;" />'))
            ->brandLogoHeight('36px')
            ->breadcrumbs(false)
            ->topNavigation()
            ->discoverResources(in: app_path('Filament/Student/Resources'), for: 'App\Filament\Student\Resources')
            ->discoverPages(in: app_path('Filament/Student/Pages'), for: 'App\Filament\Student\Pages')
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
            // ->darkMode(false)
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
