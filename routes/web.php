<?php

use App\Http\Controllers\Auth\AcceptInvitationController;
use App\Http\Controllers\Site\CarteController;
use App\Http\Controllers\Site\ChatController;
use App\Http\Controllers\Site\ContactController;
use App\Http\Controllers\Site\GalerieController;
use App\Http\Controllers\Site\HomeController;
use App\Http\Controllers\Site\LegalPageController;
use App\Http\Controllers\Site\ReservationController;
use App\Http\Controllers\Site\SitemapController;
use App\Http\Controllers\Site\SitePostController;
use Illuminate\Support\Facades\Route;

Route::get('/invitation/{token}', [AcceptInvitationController::class, 'show'])->name('invitation.show');
Route::post('/invitation/{token}', [AcceptInvitationController::class, 'store'])
    ->middleware('throttle:12,1')
    ->name('invitation.store');

Route::middleware(['tenant', 'published', 'record_site_page_view'])->group(function (): void {
    Route::get('/sitemap.xml', SitemapController::class)->name('site.sitemap');
    Route::get('/robots.txt', static fn () => response(
        'User-agent: *'.PHP_EOL.'Allow: /'.PHP_EOL.'Sitemap: '.url('/sitemap.xml').PHP_EOL,
        200,
        ['Content-Type' => 'text/plain; charset=UTF-8'],
    ))->name('site.robots');

    Route::get('/', HomeController::class)->name('site.home');
    Route::get('/carte', CarteController::class)->name('site.carte');
    Route::get('/galerie', GalerieController::class)->name('site.galerie');
    Route::get('/contact', [ContactController::class, 'show'])->name('site.contact');
    Route::post('/contact', [ContactController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('site.contact.store');
    Route::post('/chat/message', [ChatController::class, 'store'])
        ->middleware('throttle:chat')
        ->name('site.chat.store');
    Route::get('/reservation', [ReservationController::class, 'show'])->name('site.reservation');
    Route::get('/reservation/availability', [ReservationController::class, 'availability'])->name('site.reservation.availability');
    Route::post('/reservation', [ReservationController::class, 'store'])->name('site.reservation.store');
    Route::get('/reservation/manage/{token}', [ReservationController::class, 'manage'])->name('site.reservation.manage');
    Route::post('/reservation/manage/{token}/cancel', [ReservationController::class, 'cancelByToken'])->name('site.reservation.cancel');
    Route::post('/reservation/manage/{token}/reschedule', [ReservationController::class, 'rescheduleByToken'])->name('site.reservation.reschedule');

    Route::get('/actualites', [SitePostController::class, 'index'])->name('site.posts.index');
    Route::get('/actualites/{slug}', [SitePostController::class, 'show'])->name('site.posts.show');
    Route::get('/legal/{slug}', [LegalPageController::class, 'show'])->name('site.legal');
});
