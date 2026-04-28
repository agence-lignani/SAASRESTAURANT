<?php

namespace App\Services\MenuImport;

use App\Contracts\MenuImport\MenuImportExtractor;
use App\Models\MenuPhotoImport;

/**
 * Brouillon fixe (tests / secours sans Tesseract).
 */
final class StubMenuImportExtractor implements MenuImportExtractor
{
    /**
     * Génère un brouillon d’exemple. La photo enregistrée n’est pas analysée (pas d’OCR).
     *
     * @return array<string, mixed>
     */
    public function extractForImport(MenuPhotoImport $import): array
    {
        $rid = $import->restaurant_id;

        return [
            'categories' => [
                [
                    'name' => 'Entrées',
                    'description' => null,
                    'items' => [
                        ['name' => 'Velouté du jour', 'price' => '7.00', 'description' => 'À confirmer avec la carte réelle.'],
                        ['name' => 'Salade verte', 'price' => '9.50', 'description' => ''],
                    ],
                ],
                [
                    'name' => 'Plats',
                    'description' => null,
                    'items' => [
                        ['name' => 'Plat du jour', 'price' => '16.00', 'description' => 'Demander en salle.'],
                    ],
                ],
            ],
            'meta' => [
                'source' => 'stub_j3',
                'restaurant_id' => $rid,
                'extraction_mode' => 'stub_no_ocr',
                'stored_disk' => $import->disk,
                'stored_path' => $import->path,
                'hint_fr' => 'Ce JSON est un exemple fixe : le fichier image n’est pas lu (aucun OCR en J3). Remplacez le contenu par votre carte ou branchez un service d’extraction.',
                'hint' => 'Brouillon automatique. Éditez le JSON ou les plats ci-dessous avant d’appliquer à la carte.',
            ],
        ];
    }
}
