<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'resolve.tenant' => \App\Http\Middleware\ResolveTenant::class,
            'resolve.company' => \App\Http\Middleware\ResolveCompany::class,
            'admin' => \App\Http\Middleware\EnsureAdmin::class,
            'password.changed' => \App\Http\Middleware\EnsurePasswordChanged::class,
            'permission' => \App\Http\Middleware\EnsurePermission::class,
        ]);

        // Logged-in users still holding a temporary password are funnelled to the
        // change-password screen before they can reach any portal.
        $middleware->appendToGroup('web', \App\Http\Middleware\EnsurePasswordChanged::class);

        // Unauthenticated users are sent to the login for the area they hit.
        $middleware->redirectGuestsTo(fn ($request) => $request->is('company*')
            ? route('company.login')
            : route('admin.login'));

        // EnsureAdmin (platform mode) and ResolveTenant (tenant scope) both set
        // TenantContext, which decides what the global scope shows. They MUST run
        // before SubstituteBindings so route-model binding of tenant-scoped models
        // (Page/Collection/Record) resolves under the correct scope.
        $middleware->priority([
            \Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests::class,
            \Illuminate\Cookie\Middleware\EncryptCookies::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Auth\Middleware\Authenticate::class,
            \App\Http\Middleware\EnsureAdmin::class,
            \App\Http\Middleware\ResolveTenant::class,
            \App\Http\Middleware\ResolveCompany::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \Illuminate\Auth\Middleware\Authorize::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();
