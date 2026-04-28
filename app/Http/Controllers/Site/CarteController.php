<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Site\Concerns\PreparesBistroPublicPage;
use App\Models\MenuCategory;
use App\Models\Restaurant;
use App\Support\SiteContent\SiteContentResolver;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CarteController extends Controller
{
    use PreparesBistroPublicPage;

    public function __invoke(Request $request): View
    {
        /** @var Restaurant $restaurant */
        $restaurant = $request->attributes->get('restaurant');
        $restaurant->loadMissing('pageContent');
        $siteContent = SiteContentResolver::forRestaurant($restaurant);

        $categories = MenuCategory::query()
            ->where('restaurant_id', $restaurant->id)
            ->orderBy('sort_order')
            ->with([
                'menuItems' => fn ($q) => $q
                    ->where('is_available', true)
                    ->orderBy('sort_order'),
            ])
            ->get();

        return view('site.carte', array_merge($this->bistroThemePayload($restaurant), [
            'categories' => $categories,
            'pageContent' => $siteContent['carte'],
        ]));
    }
}
