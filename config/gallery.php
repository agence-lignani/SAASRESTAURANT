<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Galerie — limites performance & fichiers
    |--------------------------------------------------------------------------
    |
    | Utilisé par le back-office (Filament) et documenté sur la liste des médias.
    | max_upload_kb : poids max par fichier (Ko). max_items : 0 = illimité.
    |
    */

    /** Aligné sur config/image_upload.php (IMAGE_UPLOAD_MAX_KB). */
    'max_upload_kb' => max(64, (int) env('IMAGE_UPLOAD_MAX_KB', 500)),

    'accepted_mime_types' => [
        'image/jpeg',
        'image/png',
        'image/webp',
    ],

    /** Extensions affichées dans l’admin (info seulement). */
    'accepted_extensions_label' => 'JPEG, PNG, WebP',

    /** Nombre max de photos par établissement (0 = pas de limite). */
    'max_items_per_restaurant' => max(0, (int) env('GALLERY_MAX_ITEMS', 36)),

];
