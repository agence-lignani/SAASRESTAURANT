<?php

namespace App\Services\MenuImport;

use App\Contracts\MenuImport\MenuImportExtractor;
use App\Models\MenuPhotoImport;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;
use Throwable;

final class TesseractMenuImportExtractor implements MenuImportExtractor
{
    /**
     * Chemins usuels : PHP (php-fpm, Herd, GUI) n’a souvent pas /opt/homebrew/bin dans le PATH.
     *
     * @var list<string>
     */
    private const TESSERACT_CANDIDATES = [
        '/opt/homebrew/bin/tesseract',
        '/usr/local/bin/tesseract',
        '/usr/bin/tesseract',
    ];

    public function __construct(
        private readonly MenuOcrTextParser $parser = new MenuOcrTextParser,
    ) {}

    public function extractForImport(MenuPhotoImport $import): array
    {
        $binary = $this->resolveTesseractBinary();
        [$imagePath, $cleanupImage] = $this->resolveImagePath($import);

        $languages = (string) config('menu_import.tesseract.languages', 'fra+eng');
        $psm = (string) config('menu_import.tesseract.psm', '6');
        $timeout = (float) config('menu_import.tesseract.timeout', 120);

        $command = array_values(array_filter([
            $binary,
            $imagePath,
            'stdout',
            '-l',
            $languages,
            '--psm',
            $psm,
        ]));

        $process = new Process($command);
        $process->setTimeout($timeout);

        $raw = '';
        try {
            try {
                $process->mustRun();
            } catch (Throwable $e) {
                $err = trim($process->getErrorOutput().' '.$process->getOutput());

                throw new RuntimeException(
                    'Tesseract OCR a échoué. Installez Tesseract (ex. macOS : brew install tesseract tesseract-lang) '
                    .'et les paquets de langue « fra » / « eng ». Détail : '.($err !== '' ? $err : $e->getMessage())
                );
            }

            $raw = trim($process->getOutput());
        } finally {
            if ($cleanupImage) {
                @unlink($imagePath);
            }
        }

        if ($raw === '') {
            throw new RuntimeException(
                'Aucun texte détecté sur l’image (OCR vide). Essayez une photo plus nette, mieux éclairée, ou un autre mode PSM (config menu_import.tesseract.psm).'
            );
        }

        $categories = $this->parser->categoriesFromText($raw);

        return [
            'categories' => $categories,
            'meta' => [
                'source' => 'tesseract_ocr',
                'restaurant_id' => $import->restaurant_id,
                'extraction_mode' => 'ocr_tesseract',
                'stored_disk' => $import->disk,
                'stored_path' => $import->path,
                'tesseract' => [
                    'languages' => $languages,
                    'psm' => $psm,
                ],
                'ocr_char_count' => mb_strlen($raw),
                'ocr_preview' => mb_substr($raw, 0, 400),
                'hint_fr' => 'Texte issu de l’OCR : vérifiez chaque plat et prix avant d’appliquer à la carte.',
                'hint' => 'OCR draft — review names and prices before applying.',
            ],
        ];
    }

    private function resolveTesseractBinary(): string
    {
        $configured = config('menu_import.tesseract.binary');
        if (is_string($configured) && $configured !== '' && is_executable($configured)) {
            return $configured;
        }

        foreach (self::TESSERACT_CANDIDATES as $path) {
            if (is_executable($path)) {
                return $path;
            }
        }

        $found = (new ExecutableFinder)->find('tesseract', null, ['/opt/homebrew/bin', '/usr/local/bin', '/usr/bin']);

        if ($found !== null) {
            return $found;
        }

        throw new RuntimeException(
            'Binaire « tesseract » introuvable. Installez-le (ex. brew install tesseract tesseract-lang) '
            .'ou définissez MENU_IMPORT_TESSERACT_BINARY dans .env.'
        );
    }

    /**
     * @return array{0: string, 1: bool} Chemin fichier, true si fichier temporaire à supprimer
     */
    private function resolveImagePath(MenuPhotoImport $import): array
    {
        $disk = Storage::disk($import->disk);
        $relative = $import->path;

        if ($relative === null || $relative === '') {
            throw new RuntimeException('Chemin d’image d’import manquant.');
        }

        if (! $disk->exists($relative)) {
            throw new RuntimeException('Fichier image introuvable sur le disque : '.$relative);
        }

        if (method_exists($disk, 'path')) {
            /** @phpstan-ignore-next-line */
            $local = $disk->path($relative);
            if (is_string($local) && is_file($local)) {
                return [$local, false];
            }
        }

        $tmp = tempnam(sys_get_temp_dir(), 'menu_ocr_');
        if ($tmp === false) {
            throw new RuntimeException('Impossible de créer un fichier temporaire pour l’OCR.');
        }

        $stream = $disk->readStream($relative);
        if ($stream === false) {
            throw new RuntimeException('Lecture du fichier image impossible.');
        }

        try {
            $contents = stream_get_contents($stream);
        } finally {
            if (is_resource($stream)) {
                fclose($stream);
            }
        }

        if ($contents === false || @file_put_contents($tmp, $contents) === false) {
            @unlink($tmp);

            throw new RuntimeException('Copie temporaire de l’image pour l’OCR impossible.');
        }

        return [$tmp, true];
    }
}
