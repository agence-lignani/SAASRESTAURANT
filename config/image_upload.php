<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Images — poids cible après compression (Ko)
    |--------------------------------------------------------------------------
    |
    | Toutes les images uploadées dans l’admin sont réduites pour rester
    | en dessous de ce plafond (compression JPEG + réduction de taille si besoin).
    |
    */

    'max_kilobytes' => max(64, (int) env('IMAGE_UPLOAD_MAX_KB', 500)),

    /*
    |--------------------------------------------------------------------------
    | Taille max acceptée côté navigateur avant compression (Ko)
    |--------------------------------------------------------------------------
    |
    | Les fichiers plus lourds sont refusés avant traitement (évite les OOM).
    |
    */

    'max_client_upload_kilobytes' => max(2048, (int) env('IMAGE_UPLOAD_MAX_CLIENT_KB', 10240)),

];
