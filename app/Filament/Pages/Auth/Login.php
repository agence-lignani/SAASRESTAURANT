<?php

namespace App\Filament\Pages\Auth;

use App\Models\User;
use App\Services\Auth\FilamentLoginAttemptLimiter;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Actions\Action;
use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Facades\Filament;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\OneTimeCodeInput;
use Filament\Forms\Components\TextInput;
use Filament\Models\Contracts\FilamentUser;
use Filament\Schemas\Components\Callout;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Illuminate\Auth\SessionGuard;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Connexion en un seul écran : nom de famille + code à 6 chiffres (mot de passe).
 */
class Login extends BaseLogin
{
    protected static string $layout = 'filament.components.layouts.adobe-login';

    protected string $view = 'filament.pages.auth.adobe-login';

    /** Pleine largeur pour laisser le fond gris du gabarit visible sur les côtés de la carte. */
    protected Width|string|null $maxWidth = Width::Full;

    public function mount(): void
    {
        if (Filament::auth()->check()) {
            redirect()->intended(Filament::getUrl());
        }

        parent::mount();
    }

    public function hasLogo(): bool
    {
        return false;
    }

    public function getHeading(): string|Htmlable|null
    {
        return null;
    }

    public function getSubheading(): string|Htmlable|null
    {
        return null;
    }

    public function getTitle(): string|Htmlable
    {
        return 'Connexion — '.config('app.name');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Callout::make('Démonstration (local)')
                    ->description(new HtmlString(
                        '<ul class="mt-1 list-disc space-y-1 ps-4 text-sm">'
                        .'<li><strong>Gérant</strong> : nom <code class="rounded bg-gray-950/5 px-1 py-0.5 text-xs dark:bg-white/10">LIGNANI</code> — code <code class="rounded bg-gray-950/5 px-1 py-0.5 text-xs dark:bg-white/10">123456</code></li>'
                        .'<li><strong>Salle</strong> — code <code class="rounded bg-gray-950/5 px-1 py-0.5 text-xs dark:bg-white/10">222222</code></li>'
                        .'<li><strong>Serveur</strong> — code <code class="rounded bg-gray-950/5 px-1 py-0.5 text-xs dark:bg-white/10">333333</code></li>'
                        .'</ul>'
                        .'<p class="mt-2 text-xs text-gray-600 dark:text-gray-400">Après <code class="text-xs">php artisan db:seed</code>, le champ <strong>Nom</strong> correspond au nom de famille du compte démo.</p>'
                    ))
                    ->info()
                    ->visible(fn (): bool => app()->isLocal())
                    ->columnSpanFull(),
                $this->getFamilyNameFormComponent(),
                $this->getCodeFormComponent(),
                $this->getRememberFormComponent(),
            ]);
    }

    protected function getFamilyNameFormComponent(): Component
    {
        return TextInput::make('family_name')
            ->label('Nom')
            ->required()
            ->maxLength(120)
            ->trim()
            ->autocomplete('family-name')
            ->autofocus();
    }

    protected function getCodeFormComponent(): Component
    {
        return OneTimeCodeInput::make('code')
            ->label('Code')
            ->required()
            ->length(6)
            ->helperText('Six chiffres — le même code que celui défini à l’acceptation de l’invitation.')
            ->columnSpanFull();
    }

    protected function getRememberFormComponent(): Component
    {
        return Checkbox::make('remember')
            ->label('Rester connecté·e');
    }

    /**
     * @return array<Action>
     */
    protected function getFormActions(): array
    {
        return [
            $this->getAuthenticateFormAction()
                ->label('Se connecter'),
        ];
    }

    public function authenticate(): ?LoginResponse
    {
        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            $this->getRateLimitedNotification($exception)?->send();

            return null;
        }

        /** @var SessionGuard $authGuard */
        $authGuard = Filament::auth();

        $data = $this->form->getState();
        $familyName = trim((string) ($data['family_name'] ?? ''));
        $code = (string) ($data['code'] ?? '');

        $limiter = app(FilamentLoginAttemptLimiter::class);
        $limiter->ensureNotLocked($familyName);

        $user = $this->findUserByFamilyNameAndCode($familyName, $code);

        if (! $user) {
            $this->fireFailedEvent($authGuard, null, ['family_name' => $familyName, 'password' => $code]);
            $this->throwAuthFailureWithAttemptWarning($familyName);
        }

        if (! ($user instanceof FilamentUser) || ! $user->canAccessPanel(Filament::getCurrentOrDefaultPanel())) {
            $this->fireFailedEvent($authGuard, $user, ['family_name' => $familyName, 'password' => $code]);
            $this->throwAuthFailureWithAttemptWarning($familyName);
        }

        $profileRole = $this->resolveFilamentProfileRole($user);

        $authGuard->login($user, (bool) ($data['remember'] ?? false));
        $limiter->clear($familyName);
        session(['filament_profile_role' => $profileRole]);
        session()->regenerate();

        return app(LoginResponse::class);
    }

    /**
     * @throws ValidationException
     */
    private function throwAuthFailureWithAttemptWarning(string $familyName): never
    {
        $limiter = app(FilamentLoginAttemptLimiter::class);
        $limiter->recordFailure($familyName);
        $lockoutMinutes = (int) config('filament_login.lockout_minutes', 30);

        if ($limiter->isLocked($familyName)) {
            $until = $limiter->lockedUntil($familyName);
            $when = $until !== null
                ? 'Réessayez après le '.$until->locale('fr')->isoFormat('D MMMM YYYY à HH:mm').'.'
                : "Réessayez dans {$lockoutMinutes} minutes.";

            throw ValidationException::withMessages([
                'data.family_name' => "Connexion bloquée pendant {$lockoutMinutes} minutes suite à trop de tentatives infructueuses. {$when}",
                'data.code' => 'Nom ou code incorrect. Trop de tentatives : compte temporairement bloqué.',
            ]);
        }

        $remaining = $limiter->remainingFailuresBeforeLock($familyName);
        $suffix = $this->loginAttemptsRemainingUserMessage($remaining, $lockoutMinutes);

        throw ValidationException::withMessages([
            'data.code' => 'Nom ou code incorrect. Vérifiez vos saisies.'.$suffix,
        ]);
    }

    private function loginAttemptsRemainingUserMessage(int $remaining, int $lockoutMinutes): string
    {
        if ($remaining <= 0) {
            return '';
        }

        if ($remaining === 1) {
            return " Attention : en cas de nouvel échec, la connexion sera bloquée pendant {$lockoutMinutes} minutes.";
        }

        return " Attention : il vous reste {$remaining} tentatives avant un blocage de {$lockoutMinutes} minutes.";
    }

    private function findUserByFamilyNameAndCode(string $familyName, string $code): ?User
    {
        if ($familyName === '' || ! preg_match('/^\d{6}$/', $code)) {
            return null;
        }

        $needle = Str::lower($familyName);

        $candidates = User::query()
            ->whereNotNull('family_name')
            ->whereRaw('lower(trim(family_name)) = ?', [$needle])
            ->get();

        foreach ($candidates as $candidate) {
            if (Hash::check($code, $candidate->password)) {
                return $candidate;
            }
        }

        return null;
    }

    private function resolveFilamentProfileRole(User $user): string
    {
        $restaurant = $user->restaurants()->orderBy('restaurants.id')->first();

        if ($restaurant === null) {
            return 'owner';
        }

        return (string) ($restaurant->pivot->role ?? 'owner');
    }
}
