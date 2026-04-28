<?php

namespace Tests\Unit;

use App\Support\Media\ImageCompressor;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ImageCompressorTest extends TestCase
{
    #[Test]
    public function test_does_nothing_when_file_already_under_limit(): void
    {
        if (! extension_loaded('gd')) {
            $this->markTestSkipped('GD required');
        }

        $path = $this->writeTempJpeg(40, 40, 90);

        $before = filesize($path);
        $hint = ImageCompressor::compressToMaxBytes($path, 512_000);
        $after = filesize($path);

        $this->assertNull($hint);
        $this->assertSame($before, $after);

        @unlink($path);
    }

    #[Test]
    public function test_compresses_large_image_below_max_bytes(): void
    {
        if (! extension_loaded('gd')) {
            $this->markTestSkipped('GD required');
        }

        $path = $this->writeTempJpeg(5000, 5000, 100);

        $before = filesize($path);
        if ($before <= 512_000) {
            @unlink($path);
            $this->markTestSkipped('Could not generate a JPEG larger than 500 KiB on this environment');

            return;
        }

        $hint = ImageCompressor::compressToMaxBytes($path, 512_000);
        $after = filesize($path);

        $this->assertLessThanOrEqual(512_000, $after);
        $this->assertLessThan($before, $after);

        @unlink($path);
    }

    private function writeTempJpeg(int $width, int $height, int $quality): string
    {
        $path = sys_get_temp_dir().'/img_compress_test_'.uniqid('', true).'.jpg';
        $img = imagecreatetruecolor($width, $height);
        $bg = imagecolorallocate($img, 200, 100, 50);
        imagefill($img, 0, 0, $bg);
        for ($y = 0; $y < $height; $y += 40) {
            $c = imagecolorallocate($img, $y % 256, ($y * 3) % 256, 0);
            imageline($img, 0, $y, $width, $y, $c);
        }
        imagejpeg($img, $path, $quality);
        imagedestroy($img);

        return $path;
    }
}
