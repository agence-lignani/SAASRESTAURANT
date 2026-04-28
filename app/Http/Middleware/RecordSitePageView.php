<?php

namespace App\Http\Middleware;

use App\Models\Restaurant;
use App\Models\SitePageView;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Enregistre un trafic minimal par page (GET) pour analytics J7 — pas de cookie marketing.
 */
class RecordSitePageView
{
    private const TRACKED_ROUTE_NAMES = [
        'site.home',
        'site.carte',
        'site.galerie',
        'site.contact',
        'site.reservation',
        'site.posts.index',
        'site.posts.show',
        'site.legal',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($response->getStatusCode() !== 200 || ! $request->isMethod('GET')) {
            return $response;
        }

        $routeName = $request->route()?->getName();
        if ($routeName === null || ! in_array($routeName, self::TRACKED_ROUTE_NAMES, true)) {
            return $response;
        }

        /** @var Restaurant|null $restaurant */
        $restaurant = $request->attributes->get('restaurant');
        if ($restaurant === null) {
            return $response;
        }

        $path = '/'.ltrim($request->path(), '/');
        if ($path === '//') {
            $path = '/';
        }
        if (strlen($path) > 512) {
            $path = substr($path, 0, 512);
        }

        SitePageView::query()->create([
            'restaurant_id' => $restaurant->id,
            'path' => $path,
            'route_name' => $routeName,
            'viewed_at' => now(),
        ]);

        return $response;
    }
}
