<?php

namespace App\Filament\Support;

use App\Support\Media\ImageCompressor;
use Filament\Forms\Components\BaseFileUpload;
use Filament\Forms\Components\FileUpload;
use League\Flysystem\UnableToCheckFileExistence;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

final class FilamentImageUpload
{
    /**
     * Compresse chaque image vers le plafond configuré avant enregistrement sur le disque.
     */
    public static function withAutomaticCompression(FileUpload $field): FileUpload
    {
        $maxBytes = max(50_000, (int) config('image_upload.max_kilobytes', 500) * 1024);
        $maxClientKb = (int) config('image_upload.max_client_upload_kilobytes', 10240);

        return $field
            ->maxSize($maxClientKb)
            ->saveUploadedFileUsing(function (BaseFileUpload $component, TemporaryUploadedFile $file) use ($maxBytes): ?string {
                $realPath = $file->getRealPath();
                $storageName = $component->getUploadedFileNameForStorage($file);

                if ($realPath && is_file($realPath)) {
                    $hint = ImageCompressor::compressToMaxBytes($realPath, $maxBytes);
                    if ($hint === 'jpg') {
                        $storageName = (string) preg_replace('/\.[^.]+$/i', '.jpg', $storageName);
                    }
                }

                try {
                    if (! $file->exists()) {
                        return null;
                    }
                } catch (UnableToCheckFileExistence $exception) {
                    return null;
                }

                if (
                    $component->shouldMoveFiles() &&
                    ($component->getDiskName() === (fn (): string => $this->disk)->call($file))
                ) {
                    $newPath = trim($component->getDirectory().'/'.$storageName, '/');
                    $component->getDisk()->move((fn (): string => $this->path)->call($file), $newPath);

                    return $newPath;
                }

                $path = $file->storeAs(
                    $component->getDirectory(),
                    $storageName,
                    $component->getDiskName(),
                );

                if ($component->getVisibility() === 'public') {
                    rescue(fn () => $component->getDisk()->setVisibility($path, 'public'), report: false);
                }

                return $path;
            });
    }
}
