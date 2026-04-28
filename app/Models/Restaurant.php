<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Restaurant extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'public_host',
        'tagline',
        'contact_email',
        'contact_phone',
        'address_line1',
        'address_line2',
        'city',
        'postal_code',
        'country',
        'parking_info',
        'accessibility_info',
        'facebook_url',
        'instagram_url',
        'twitter_url',
        'linkedin_url',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }

    public function isPublished(): bool
    {
        return $this->published_at !== null;
    }

    /**
     * @return BelongsToMany<User, $this>
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * @return HasOne<RestaurantThemeSetting, $this>
     */
    public function themeSetting(): HasOne
    {
        return $this->hasOne(RestaurantThemeSetting::class);
    }

    /**
     * @return HasMany<OpeningHour, $this>
     */
    public function openingHours(): HasMany
    {
        return $this->hasMany(OpeningHour::class)->orderBy('day_of_week');
    }

    /**
     * @return HasMany<OpeningHourException, $this>
     */
    public function openingHourExceptions(): HasMany
    {
        return $this->hasMany(OpeningHourException::class)->orderBy('exception_date');
    }

    /**
     * @return HasMany<MenuCategory, $this>
     */
    public function menuCategories(): HasMany
    {
        return $this->hasMany(MenuCategory::class)->orderBy('sort_order');
    }

    /**
     * @return HasMany<MenuItem, $this>
     */
    public function menuItems(): HasMany
    {
        return $this->hasMany(MenuItem::class);
    }

    /**
     * @return HasMany<GalleryMedia, $this>
     */
    public function galleryMedia(): HasMany
    {
        return $this->hasMany(GalleryMedia::class)->orderBy('sort_order');
    }

    /**
     * @return HasMany<ContactMessage, $this>
     */
    public function contactMessages(): HasMany
    {
        return $this->hasMany(ContactMessage::class)->latest();
    }

    /**
     * @return HasMany<MenuPhotoImport, $this>
     */
    public function menuPhotoImports(): HasMany
    {
        return $this->hasMany(MenuPhotoImport::class)->latest();
    }

    /**
     * @return HasOne<BookingSetting, $this>
     */
    public function bookingSetting(): HasOne
    {
        return $this->hasOne(BookingSetting::class);
    }

    /**
     * @return HasMany<BookingService, $this>
     */
    public function bookingServices(): HasMany
    {
        return $this->hasMany(BookingService::class)->orderBy('sort_order');
    }

    /**
     * @return HasMany<Reservation, $this>
     */
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class)->latest('reservation_at');
    }

    /**
     * @return HasOne<RestaurantPageContent, $this>
     */
    public function pageContent(): HasOne
    {
        return $this->hasOne(RestaurantPageContent::class);
    }

    /**
     * @return HasMany<LegalPage, $this>
     */
    public function legalPages(): HasMany
    {
        return $this->hasMany(LegalPage::class);
    }

    /**
     * @return HasMany<SitePost, $this>
     */
    public function sitePosts(): HasMany
    {
        return $this->hasMany(SitePost::class)->latest('published_at');
    }

    /**
     * @return HasMany<UserInvitation, $this>
     */
    public function userInvitations(): HasMany
    {
        return $this->hasMany(UserInvitation::class)->latest();
    }

    /**
     * @return HasOne<RestaurantChatSetting, $this>
     */
    public function chatSetting(): HasOne
    {
        return $this->hasOne(RestaurantChatSetting::class);
    }

    /**
     * @return HasMany<SitePageView, $this>
     */
    public function sitePageViews(): HasMany
    {
        return $this->hasMany(SitePageView::class);
    }
}
