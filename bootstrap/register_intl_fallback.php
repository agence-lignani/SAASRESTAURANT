<?php

declare(strict_types=1);

/**
 * Sans ext-intl, la classe Laravel Illuminate\Support\Number lève une exception
 * dès qu'un formatage localisé est demandé (Filament tables, pagination, etc.).
 * On enregistre un autoloader en tête de pile pour fournir une implémentation
 * de secours uniquement dans ce cas.
 */
if (extension_loaded('intl')) {
    return;
}

spl_autoload_register(static function (string $class): bool {
    if ($class !== 'Illuminate\Support\Number') {
        return false;
    }

    require __DIR__.'/Compatibility/Illuminate/Support/Number.php';

    return true;
}, true, true);
