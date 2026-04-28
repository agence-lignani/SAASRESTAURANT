<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Site public : si l’établissement n’est pas publié, page d’attente (J2 publication).
 */
class EnsureRestaurantPublished
{
    public function handle(Request $request, Closure $next): Response
    {
        $restaurant = $request->attributes->get('restaurant');

        if ($restaurant === null || ! $restaurant->isPublished()) {
            return response()
                ->view('site.not-published', ['restaurant' => $restaurant])
                ->setStatusCode(503);
        }

        return $next($request);
    }
}
