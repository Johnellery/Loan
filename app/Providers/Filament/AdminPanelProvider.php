<?php

namespace App\Providers\Filament;

use Awcodes\FilamentQuickCreate\QuickCreatePlugin;
use BezhanSalleh\FilamentLanguageSwitch\FilamentLanguageSwitchPlugin;
use Filament\Forms\Components\FileUpload;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Filament\Resources;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Njxqlus\FilamentProgressbar\FilamentProgressbarPlugin;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Jeffgreco13\FilamentBreezy\BreezyCore;
use Illuminate\Validation\Rules\Password;
class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->passwordReset()
            // ->registration()
            ->sidebarFullyCollapsibleOnDesktop()
            ->emailVerification()
            ->colors([
                'primary' => Color::Indigo,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            // ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                // Widgets\AccountWidget::class,
                // Widgets\FilamentInfoWidget::class,
                // \Resources\UserResource\Widgets\UserOverview::class,
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
            // ->authGuard('user')
            ->plugins([
                QuickCreatePlugin::make()
                ->excludes([
                    // RickDBCN\FilamentEmail\Filament\Resources\EmailResource::class,
                ]),
                // new \RickDBCN\FilamentEmail\FilamentEmail(),
                // FilamentLanguageSwitchPlugin::make(),
                BreezyCore::make()
                ->avatarUploadComponent(fn() => FileUpload::make('avatar_url')->disk('public'))
                ->enableTwoFactorAuthentication(
                    force: false,
                )
                ->myProfile(
                    shouldRegisterUserMenu: true,
                    shouldRegisterNavigation: false,
                    hasAvatars: true,
                    slug: 'my-profile'
                )
                ->passwordUpdateRules(
                    rules: [Password::default()->mixedCase()->uncompromised(3)],
                    requiresCurrentPassword: true,
                    )
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->favicon(asset('images/ritaa.png'));


    }
}
