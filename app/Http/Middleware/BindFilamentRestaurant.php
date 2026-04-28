<?php

namespace App\Http\Middleware;

use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Associe l’établissement courant après authentification Filament (pivot + profil F1 en session).
 */
class BindFilamentRestaurant
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check()) {
            return $next($request);
        }

        $user = auth()->user();
        $role = session('filament_profile_role');

        $query = $user->restaurants()->orderBy('restaurants.id');

        if (filled($role)) {
            $query->wherePivot('role', $role);
        }

        $restaurant = $query->first();

        if ($restaurant === null) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            $loginUrl = Filament::getLoginUrl();

            return redirect()->to($loginUrl ?? url('/admin/login'))
                ->withErrors(['data.code' => 'Aucun établissement accessible avec ce compte. Contactez un gérant ou réessayez avec un autre accès.']);
        }

        app()->instance('filament.restaurant', $restaurant);

        return $next($request);
    }
}
