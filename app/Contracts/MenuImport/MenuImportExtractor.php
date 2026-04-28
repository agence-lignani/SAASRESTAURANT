<?php

namespace App\Contracts\MenuImport;

use App\Models\MenuPhotoImport;

interface MenuImportExtractor
{
    /**
     * @return array<string, mixed>
     */
    public function extractForImport(MenuPhotoImport $import): array;
}
