<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Driver d’extraction des photos de carte
    |--------------------------------------------------------------------------
    |
    | tesseract — OCR local (binaire « tesseract » + langues fra/eng recommandées).
    | stub — brouillon démo fixe (CI, environnement sans OCR).
    |
    */

    'driver' => env('MENU_IMPORT_DRIVER', 'tesseract'),

    'tesseract' => [
        'binary' => env('MENU_IMPORT_TESSERACT_BINARY'),
        'languages' => env('MENU_IMPORT_TESSERACT_LANG', 'fra+eng'),
        'psm' => env('MENU_IMPORT_TESSERACT_PSM', '6'),
        'timeout' => (float) env('MENU_IMPORT_TESSERACT_TIMEOUT', 120),
    ],

];
