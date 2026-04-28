<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Site\Concerns\PreparesBistroPublicPage;
use App\Models\GalleryMedia;
use App\Models\Restaurant;
use App\Support\SiteContent\SiteContentResolver;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GalerieController extends Controller
{
    use PreparesBistroPublicPage;

    public function __invoke(Request $request): View
    {
        /** @var Restaurant $restaurant */
        $restaurant = $request->attributes->get('restaurant');
        $restaurant->loadMissing('pageContent');
        $siteContent = SiteContentResolver::forRestaurant($restaurant);

        $media = GalleryMedia::query()
            ->where('restaurant_id', $restaurant->id)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view('site.galerie', array_merge($this->bistroThemePayload($restaurant), [
            'galleryMedia' => $media,
            'pageContent' => $siteContent['galerie'],
        ]));
    }
}
