<?php

namespace App\Support;

// Normalizes product screenshots so the storefront gallery never has to
// crop or letterbox at render time: every stored image is exactly 16:9 at
// WIDTH×HEIGHT, center-cropped and re-encoded with GD.
class Screenshot
{
    public const WIDTH = 1280;

    public const HEIGHT = 720;

    /**
     * Returns the re-encoded binary, or null when GD is unavailable or the
     * data cannot be decoded — callers keep the original file in that case.
     */
    public static function normalize(string $binary, string $extension): ?string
    {
        if (! function_exists('imagecreatefromstring')) {
            return null;
        }

        $source = @imagecreatefromstring($binary);

        if ($source === false) {
            return null;
        }

        $sourceWidth = imagesx($source);
        $sourceHeight = imagesy($source);

        // Largest centered 16:9 window that fits inside the source.
        $cropWidth = $sourceWidth;
        $cropHeight = (int) round($sourceWidth * self::HEIGHT / self::WIDTH);

        if ($cropHeight > $sourceHeight) {
            $cropHeight = $sourceHeight;
            $cropWidth = (int) round($sourceHeight * self::WIDTH / self::HEIGHT);
        }

        $cropX = (int) floor(($sourceWidth - $cropWidth) / 2);
        $cropY = (int) floor(($sourceHeight - $cropHeight) / 2);

        $extension = strtolower($extension);
        $canvas = imagecreatetruecolor(self::WIDTH, self::HEIGHT);

        if ($extension === 'png') {
            imagealphablending($canvas, false);
            imagesavealpha($canvas, true);
            imagefill($canvas, 0, 0, imagecolorallocatealpha($canvas, 0, 0, 0, 127));
        }

        imagecopyresampled($canvas, $source, 0, 0, $cropX, $cropY, self::WIDTH, self::HEIGHT, $cropWidth, $cropHeight);
        imagedestroy($source);

        ob_start();

        $encoded = match ($extension) {
            'png' => imagepng($canvas, null, 6),
            'webp' => function_exists('imagewebp') && imagewebp($canvas, null, 85),
            default => imagejpeg($canvas, null, 85),
        };

        $output = ob_get_clean();
        imagedestroy($canvas);

        return $encoded && $output !== false && $output !== '' ? $output : null;
    }
}
