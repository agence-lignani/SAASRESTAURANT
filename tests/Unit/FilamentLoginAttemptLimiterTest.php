<?php

namespace Tests\Unit;

use App\Services\Auth\FilamentLoginAttemptLimiter;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class FilamentLoginAttemptLimiterTest extends TestCase
{
    public function test_locks_after_max_failures_until_expiry(): void
    {
        config(['filament_login.max_attempts' => 3, 'filament_login.lockout_minutes' => 60]);
        Cache::flush();

        $limiter = new FilamentLoginAttemptLimiter;

        $limiter->recordFailure('Dupont');
        $this->assertSame(1, $limiter->failureCount('Dupont'));
        $this->assertSame(2, $limiter->remainingFailuresBeforeLock('Dupont'));

        $limiter->recordFailure('Dupont');
        $this->assertFalse($limiter->isLocked('Dupont'));
        $this->assertSame(1, $limiter->remainingFailuresBeforeLock('Dupont'));

        $limiter->recordFailure('Dupont');
        $this->assertTrue($limiter->isLocked('Dupont'));
        $this->assertSame(0, $limiter->failureCount('Dupont'));

        $limiter->clear('Dupont');
        $this->assertFalse($limiter->isLocked('Dupont'));
    }

    public function test_ensure_not_locked_throws_when_blocked(): void
    {
        config(['filament_login.max_attempts' => 1, 'filament_login.lockout_minutes' => 60]);
        Cache::flush();

        $limiter = new FilamentLoginAttemptLimiter;
        $limiter->recordFailure('Martin');

        $this->expectException(ValidationException::class);
        $limiter->ensureNotLocked('Martin');
    }

    public function test_clear_resets_failure_counter(): void
    {
        config(['filament_login.max_attempts' => 3, 'filament_login.lockout_minutes' => 60]);
        Cache::flush();

        $limiter = new FilamentLoginAttemptLimiter;
        $limiter->recordFailure('Bernard');
        $limiter->clear('Bernard');
        $limiter->recordFailure('Bernard');
        $limiter->recordFailure('Bernard');

        $this->assertFalse($limiter->isLocked('Bernard'));
    }
}
