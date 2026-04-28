<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Models\SitePost;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(Request $request): Response
    {
        /** @var Restaurant $restaurant */
        $restaurant = $request->attributes->get('restaurant');

        $urls = [
            route('site.home'),
            route('site.carte'),
            route('site.galerie'),
            route('site.contact'),
            route('site.reservation'),
            route('site.posts.index'),
            route('site.legal', ['slug' => 'mentions-legales']),
            route('site.legal', ['slug' => 'politique-de-confidentialite']),
        ];

        foreach (SitePost::query()
            ->where('restaurant_id', $restaurant->id)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->cursor() as $post) {
            $urls[] = route('site.posts.show', ['slug' => $post->slug]);
        }

        $body = view('site.sitemap-xml', ['urls' => array_unique($urls)])->render();

        return response($body, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }
}
