<?php

namespace App\Services\Auth;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;

/**
 * Limite les échecs de connexion Filament (nom de famille + code) par IP et identifiant saisi.
 */
final class FilamentLoginAttemptLimiter
{
    private const CACHE_PREFIX = 'filament_login_v1:';

    public function maxAttempts(): int
    {
        return max(1, (int) config('filament_login.max_attempts', 3));
    }

    public function lockoutMinutes(): int
    {
        return max(1, (int) config('filament_login.lockout_minutes', 60));
    }

    public function throttleKey(string $familyNameInput): string
    {
        $normalized = mb_strtolower(trim($familyNameInput));
        $ip = request()->ip() ?? 'unknown';

        return self::CACHE_PREFIX.hash('sha256', $normalized.'|'.$ip);
    }

    public function failureCount(string $familyNameInput): int
    {
        return (int) Cache::get($this->throttleKey($familyNameInput).':failures', 0);
    }

    /**
     * Nombre d’échecs encore possibles avant blocage (après le dernier enregistrement, si non bloqué).
     */
    public function remainingFailuresBeforeLock(string $familyNameInput): int
    {
        return max(0, $this->maxAttempts() - $this->failureCount($familyNameInput));
    }

    public function isLocked(string $familyNameInput): bool
    {
        $until = Cache::get($this->lockKey($familyNameInput));

        if (! is_int($until) && ! is_numeric($until)) {
            return false;
        }

        return CarbonImmutable::now()->getTimestamp() < (int) $until;
    }

    public function lockedUntil(string $familyNameInput): ?CarbonImmutable
    {
        $until = Cache::get($this->lockKey($familyNameInput));
        if (! is_int($until) && ! is_numeric($until)) {
            return null;
        }

        return CarbonImmutable::createFromTimestamp((int) $until);
    }

    /**
     * @throws ValidationException
     */
    public function ensureNotLocked(string $familyNameInput): void
    {
        if (! $this->isLocked($familyNameInput)) {
            return;
        }

        $until = $this->lockedUntil($familyNameInput);

        throw ValidationException::withMessages([
            'data.family_name' => $until !== null
                ? 'Trop de tentatives. Réessayez après le '.$until->locale('fr')->isoFormat('D MMMM YYYY à HH:mm').'.'
                : 'Trop de tentatives. Réessayez plus tard.',
        ]);
    }

    public function recordFailure(string $familyNameInput): void
    {
        $base = $this->throttleKey($familyNameInput);
        $failKey = $base.':failures';

        $failures = (int) Cache::get($failKey, 0) + 1;
        Cache::put($failKey, $failures, now()->addMinutes($this->lockoutMinutes()));

        if ($failures >= $this->maxAttempts()) {
            $until = CarbonImmutable::now()->addMinutes($this->lockoutMinutes())->getTimestamp();
            Cache::put($this->lockKey($familyNameInput), $until, now()->addMinutes($this->lockoutMinutes()));
            Cache::forget($failKey);
        }
    }

    public function clear(string $familyNameInput): void
    {
        Cache::forget($this->throttleKey($familyNameInput).':failures');
        Cache::forget($this->lockKey($familyNameInput));
    }

    private function lockKey(string $familyNameInput): string
    {
        return $this->throttleKey($familyNameInput).':locked_until';
    }
}
