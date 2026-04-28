<?php

namespace App\Support\Chat;

final class ChatIpHasher
{
    public static function hash(?string $ip): string
    {
        $ip ??= '0.0.0.0';

        return hash('sha256', $ip.'|'.config('app.key'));
    }
}
