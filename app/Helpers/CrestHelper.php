<?php

namespace App\Helpers;

use GdImage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CrestHelper
{
    /**
     * Decode a crest hex string into a GD image resource.
     */
    public static function decodeHexToImage(string $bin, ?int $width = null, ?int $height = null): GdImage
    {
        abort_if(! ctype_xdigit($bin), 404, 'Invalid crest data.');

        $width = $width ?? (int) config('crest.width', 16);
        $height = $height ?? (int) config('crest.height', 16);
        $palette = self::resolvePalette();

        $img = imagecreatetruecolor($width, $height);
        imagesavealpha($img, true);
        imagealphablending($img, true);

        $transparent = imagecolorallocatealpha($img, 0, 0, 0, 127);
        imagefill($img, 0, 0, $transparent);

        $colors = [];
        foreach ($palette as $index => $hex) {
            [$r, $g, $b] = sscanf($hex, '#%02x%02x%02x');
            $colors[(int) $index] = imagecolorallocate($img, (int) $r, (int) $g, (int) $b);
        }

        $bytes = str_split(substr($bin, 2), 2);

        foreach ($bytes as $i => $byte) {
            $y = intdiv($i, $width);
            if ($y >= $height) {
                break;
            }

            $x = $i % $width;
            $colorIndex = hexdec($byte);

            imagesetpixel(
                $img,
                $x,
                $y,
                $colors[$colorIndex] ?? $transparent
            );
        }

        imageflip($img, IMG_FLIP_VERTICAL);

        return $img;
    }

    /**
     * Stream crest image as PNG response.
     */
    public static function streamPngResponse(string $bin, ?int $width = null, ?int $height = null): StreamedResponse
    {
        $image = self::decodeHexToImage($bin, $width, $height);

        return response()->stream(
            function () use ($image): void {
                imagepng($image);
                imagedestroy($image);
            },
            200,
            ['Content-Type' => 'image/png']
        );
    }

    /**
     * Convert crest hex to PNG binary.
     */
    public static function decodeHexToPng(string $bin, ?int $width = null, ?int $height = null): string
    {
        $image = self::decodeHexToImage($bin, $width, $height);

        ob_start();
        imagepng($image);
        $png = (string) ob_get_clean();

        imagedestroy($image);

        return $png;
    }

    /**
     * Convert crest hex to data URI for inline img usage.
     */
    public static function decodeHexToDataUri(string $bin, ?int $width = null, ?int $height = null): string
    {
        $png = self::decodeHexToPng($bin, $width, $height);

        return 'data:image/png;base64,' . base64_encode($png);
    }

    /**
     * @return array<int, string>
     */
    private static function resolvePalette(): array
    {
        $palette = config('crest.palette', []);

        if (! is_array($palette) || $palette === []) {
            return self::buildRgb332Palette();
        }

        return $palette;
    }

    /**
     * Default 256 color palette using RGB332 mapping.
     *
     * @return array<int, string>
     */
    private static function buildRgb332Palette(): array
    {
        $palette = [];

        for ($i = 0; $i <= 255; $i++) {
            $r = (($i >> 5) & 0x07) * 255 / 7;
            $g = (($i >> 2) & 0x07) * 255 / 7;
            $b = ($i & 0x03) * 255 / 3;

            $palette[$i] = sprintf('#%02x%02x%02x', (int) round($r), (int) round($g), (int) round($b));
        }

        return $palette;
    }
}
