<?php

namespace App\Jobs;

use App\Contracts\MenuImport\MenuImportExtractor;
use App\Models\MenuPhotoImport;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProcessMenuPhotoImport
{
    use Queueable;

    public function __construct(
        public MenuPhotoImport $import
    ) {}

    public function handle(MenuImportExtractor $extractor): void
    {
        $this->import->refresh();

        if ($this->import->status !== MenuPhotoImport::STATUS_PENDING) {
            return;
        }

        $this->import->update(['status' => MenuPhotoImport::STATUS_PROCESSING]);

        try {
            $draft = $extractor->extractForImport($this->import);

            $this->import->update([
                'status' => MenuPhotoImport::STATUS_COMPLETED,
                'draft_json' => json_encode($draft, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
                'processed_at' => now(),
                'error_message' => null,
            ]);
        } catch (Throwable $e) {
            Log::error('menu_photo_import_failed', [
                'import_id' => $this->import->id,
                'exception' => $e->getMessage(),
            ]);

            $this->import->update([
                'status' => MenuPhotoImport::STATUS_FAILED,
                'error_message' => $e->getMessage(),
                'processed_at' => now(),
            ]);
        }
    }
}
