<?php

use App\Http\Middleware\EnsureRestaurantPublished;
use App\Http\Middleware\RecordSitePageView;
use App\Http\Middleware\ResolveRestaurantTenant;
use App\Http\Middleware\SecurityHeaders;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'tenant' => ResolveRestaurantTenant::class,
            'published' => EnsureRestaurantPublished::class,
            'record_site_page_view' => RecordSitePageView::class,
        ]);

        $middleware->appendToGroup('web', [
            SecurityHeaders::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
