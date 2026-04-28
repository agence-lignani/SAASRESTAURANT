<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Site\Concerns\PreparesBistroPublicPage;
use App\Models\LegalPage;
use App\Models\Restaurant;
use App\Support\SiteContent\SiteContentResolver;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LegalPageController extends Controller
{
    use PreparesBistroPublicPage;

    public function show(Request $request, string $slug): View
    {
        /** @var Restaurant $restaurant */
        $restaurant = $request->attributes->get('restaurant');

        $allowed = ['mentions-legales', 'politique-de-confidentialite'];
        abort_unless(in_array($slug, $allowed, true), 404);

        $page = LegalPage::query()
            ->where('restaurant_id', $restaurant->id)
            ->where('slug', $slug)
            ->first();

        $siteContent = SiteContentResolver::forRestaurant($restaurant);

        return view('site.legal', array_merge($this->bistroThemePayload($restaurant), [
            'legalPage' => $page,
            'slug' => $slug,
            'metaDescription' => $page?->title ?? ($restaurant->tagline ?? $restaurant->name),
            'pageContent' => $siteContent['contact'] ?? [],
        ]));
    }
}
