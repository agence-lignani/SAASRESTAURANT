<?php

namespace App\Support\Media;

use GdImage;

/**
 * Réduit le poids d’un fichier image sur disque pour qu’il ne dépasse pas $maxBytes.
 * Sortie privilégiée : JPEG (meilleur rapport taille / qualité pour la plupart des photos).
 *
 * @return 'jpg'|null Si non-null, l’extension du fichier stocké doit être .jpg (contenu JPEG).
 */
final class ImageCompressor
{
    public static function compressToMaxBytes(string $absolutePath, int $maxBytes): ?string
    {
        if (! extension_loaded('gd') || ! is_file($absolutePath)) {
            return null;
        }

        @ini_set('memory_limit', '512M');
        @set_time_limit(120);

        $size = @filesize($absolutePath);
        if ($size === false || $size <= $maxBytes) {
            return null;
        }

        $info = @getimagesize($absolutePath);
        if ($info === false) {
            return null;
        }

        $mime = $info['mime'] ?? '';
        $source = self::createFromFile($absolutePath);
        if ($source === false) {
            return null;
        }

        $w = imagesx($source);
        $h = imagesy($source);
        if ($w < 1 || $h < 1) {
            imagedestroy($source);

            return null;
        }

        if (function_exists('imagepalettetotruecolor')) {
            @imagepalettetotruecolor($source);
        }

        // JPEG sans canal alpha : pas besoin de dupliquer sur un fond blanc (évite le double pic mémoire).
        if (self::shouldOutputAsJpeg($mime)) {
            $work = $source;
        } else {
            $work = self::toTrueColorWithWhiteBackground($source);
            imagedestroy($source);
        }

        $scale = 1.0;
        $extensionHint = self::shouldOutputAsJpeg($mime) ? null : 'jpg';

        for ($iter = 0; $iter < 35; $iter++) {
            $tw = max(1, (int) round($w * $scale));
            $th = max(1, (int) round($h * $scale));

            // Pas de IMG_BICUBIC : sur certains builds GD, imagescale échoue pour les très grandes sources.
            $resized = ($tw !== $w || $th !== $h)
                ? imagescale($work, $tw, $th)
                : $work;

            if ($resized === false) {
                $scale *= 0.75;

                continue;
            }

            for ($q = 90; $q >= 18; $q -= 4) {
                $tmp = tempnam(sys_get_temp_dir(), 'imgc_');
                if ($tmp === false) {
                    continue;
                }

                if (! imagejpeg($resized, $tmp, $q)) {
                    unlink($tmp);

                    continue;
                }

                $written = filesize($tmp);
                if ($written !== false && $written <= $maxBytes) {
                    self::atomicReplace($tmp, $absolutePath);
                    if ($resized !== $work) {
                        imagedestroy($resized);
                    }
                    imagedestroy($work);

                    return $extensionHint;
                }

                unlink($tmp);
            }

            if ($resized !== $work) {
                imagedestroy($resized);
            }

            if (min($tw, $th) <= 160) {
                break;
            }

            $scale *= 0.88;
        }

        // Dernier recours : dimensions réduites + qualité minimale
        $last = false;
        $tw = max(1, (int) round($w * $scale * 0.75));
        $th = max(1, (int) round($h * $scale * 0.75));
        for ($attempt = 0; $attempt < 12 && $last === false; $attempt++) {
            $last = imagescale($work, $tw, $th);
            if ($last === false) {
                $tw = max(1, (int) round($tw * 0.75));
                $th = max(1, (int) round($th * 0.75));
            }
        }

        imagedestroy($work);

        if ($last !== false) {
            imagejpeg($last, $absolutePath, 18);
            imagedestroy($last);

            return $extensionHint;
        }

        return null;
    }

    private static function shouldOutputAsJpeg(string $mime): bool
    {
        return str_contains($mime, 'jpeg') || str_contains($mime, 'jpg');
    }

    /**
     * @return GdImage|false
     */
    private static function createFromFile(string $path)
    {
        $binary = @file_get_contents($path);
        if ($binary === false || $binary === '') {
            return false;
        }

        return @imagecreatefromstring($binary);
    }

    private static function toTrueColorWithWhiteBackground(GdImage $source): GdImage
    {
        $w = imagesx($source);
        $h = imagesy($source);

        if (imageistruecolor($source)) {
            $canvas = imagecreatetruecolor($w, $h);
            $white = imagecolorallocate($canvas, 255, 255, 255);
            imagefill($canvas, 0, 0, $white);
            imagealphablending($canvas, true);
            imagecopy($canvas, $source, 0, 0, 0, 0, $w, $h);

            return $canvas;
        }

        $canvas = imagecreatetruecolor($w, $h);
        $white = imagecolorallocate($canvas, 255, 255, 255);
        imagefill($canvas, 0, 0, $white);
        imagecopy($canvas, $source, 0, 0, 0, 0, $w, $h);

        return $canvas;
    }

    private static function atomicReplace(string $tmpPath, string $targetPath): void
    {
        if (PHP_OS_FAMILY === 'Windows') {
            @unlink($targetPath);
        }

        if (! @rename($tmpPath, $targetPath)) {
            @copy($tmpPath, $targetPath);
            @unlink($tmpPath);
        }
    }
}
