<?php

namespace iJobDesk\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \iJobDesk\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        \iJobDesk\Http\Middleware\TrustProxies::class,

        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        \iJobDesk\Http\Middleware\HttpsProtocol::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \iJobDesk\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            // \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \iJobDesk\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            'throttle:60,1',
            'bindings',
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \iJobDesk\Http\Middleware\RedirectIfAuthenticated::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,

        'auth.admin' => \iJobDesk\Http\Middleware\IsAdmin::class,

        'auth.admin.financial' => \iJobDesk\Http\Middleware\IsFinancial::class,
        'auth.admin.ticket' => \iJobDesk\Http\Middleware\IsTicket::class,
        'auth.admin.super'  => \iJobDesk\Http\Middleware\IsSuper::class,
        
        'auth.buyer' => \iJobDesk\Http\Middleware\IsBuyer::class,
        'auth.freelancer' => \iJobDesk\Http\Middleware\IsFreelancer::class,
        'auth.customer' => \iJobDesk\Http\Middleware\IsCustomer::class,
        'auth.security' => \iJobDesk\Http\Middleware\IsSecurity::class,
        'auth.active' => \iJobDesk\Http\Middleware\IsActive::class,
        'auth.api_v1' => \iJobDesk\Http\Middleware\IsAuthenticated4ApiV1::class
    ];
}
