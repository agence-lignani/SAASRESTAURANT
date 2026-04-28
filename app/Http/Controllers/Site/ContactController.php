<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Site\Concerns\PreparesBistroPublicPage;
use App\Mail\ContactMessageReceivedMail;
use App\Models\ContactMessage;
use App\Models\Restaurant;
use App\Support\SiteContent\SiteContentResolver;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class ContactController extends Controller
{
    use PreparesBistroPublicPage;

    public function show(Request $request): View
    {
        /** @var Restaurant $restaurant */
        $restaurant = $request->attributes->get('restaurant');
        $restaurant->loadMissing('pageContent');
        $siteContent = SiteContentResolver::forRestaurant($restaurant);

        return view('site.contact', array_merge($this->bistroThemePayload($restaurant), [
            'subjectOptions' => ContactMessage::subjectOptions(),
            'pageContent' => $siteContent['contact'],
        ]));
    }

    public function store(Request $request): RedirectResponse
    {
        /** @var Restaurant $restaurant */
        $restaurant = $request->attributes->get('restaurant');

        if ($request->filled('website')) {
            return redirect()
                ->route('site.contact')
                ->with('contact_ok', true);
        }

        $subjectKeys = implode(',', array_keys(ContactMessage::subjectOptions()));

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:64'],
            'subject' => ['required', 'string', 'in:'.$subjectKeys],
            'body' => ['required', 'string', 'max:5000'],
        ]);

        $message = ContactMessage::query()->create([
            'restaurant_id' => $restaurant->id,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'subject' => $validated['subject'],
            'body' => $validated['body'],
            'ip_address' => $request->ip(),
            'meta' => [
                'user_agent' => substr((string) $request->userAgent(), 0, 512),
            ],
        ]);

        $notifyTo = $restaurant->contact_email;
        if (filled($notifyTo)) {
            Mail::to($notifyTo)->queue(new ContactMessageReceivedMail($message, $restaurant));
        }

        return redirect()
            ->route('site.contact')
            ->with('contact_ok', true);
    }
}
