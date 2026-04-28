<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Site\Concerns\PreparesBistroPublicPage;
use App\Models\Restaurant;
use App\Models\SitePost;
use App\Support\SiteContent\SiteContentResolver;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SitePostController extends Controller
{
    use PreparesBistroPublicPage;

    public function index(Request $request): View
    {
        /** @var Restaurant $restaurant */
        $restaurant = $request->attributes->get('restaurant');

        $posts = SitePost::query()
            ->where('restaurant_id', $restaurant->id)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->latest('published_at')
            ->paginate(12);

        $siteContent = SiteContentResolver::forRestaurant($restaurant);

        return view('site.posts.index', array_merge($this->bistroThemePayload($restaurant), [
            'posts' => $posts,
            'metaDescription' => 'Actualités — '.$restaurant->name,
            'pageContent' => $siteContent['contact'] ?? [],
        ]));
    }

    public function show(Request $request, string $slug): View
    {
        /** @var Restaurant $restaurant */
        $restaurant = $request->attributes->get('restaurant');

        $post = SitePost::query()
            ->where('restaurant_id', $restaurant->id)
            ->where('slug', $slug)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->firstOrFail();

        $siteContent = SiteContentResolver::forRestaurant($restaurant);

        return view('site.posts.show', array_merge($this->bistroThemePayload($restaurant), [
            'post' => $post,
            'metaDescription' => $post->meta_description ?? $post->excerpt ?? $restaurant->tagline,
            'pageContent' => $siteContent['contact'] ?? [],
        ]));
    }
}
