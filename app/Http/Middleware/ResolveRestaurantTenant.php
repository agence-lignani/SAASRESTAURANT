<?php

namespace App\Http\Middleware;

use App\Models\Restaurant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolveRestaurantTenant
{
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();

        $restaurant = Restaurant::query()
            ->where('public_host', $host)
            ->first()
            ?? Restaurant::query()->orderBy('id')->first();

        if ($restaurant === null) {
            abort(503, 'Aucun établissement configuré. Exécutez les seeders (voir README).');
        }

        $restaurant->loadMissing('chatSetting');

        $request->attributes->set('restaurant', $restaurant);
        view()->share('restaurant', $restaurant);

        return $next($request);
    }
}
