<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Tentatives de connexion Filament
    |--------------------------------------------------------------------------
    |
    | Après ce nombre d’échecs consécutifs (mauvais nom / code), la connexion
    | est refusée pour la même combinaison IP + nom de famille pendant la durée
    | de blocage ci-dessous (30 minutes par défaut).
    |
    */

    'max_attempts' => (int) env('FILAMENT_LOGIN_MAX_ATTEMPTS', 3),

    'lockout_minutes' => (int) env('FILAMENT_LOGIN_LOCKOUT_MINUTES', 30),

];
