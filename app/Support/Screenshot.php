<?php

namespace App\Support;

use Illuminate\Support\Str;

// Normalizes product screenshots so the storefront gallery never has to
// crop or letterbox at render time: every stored image is exactly 16:9 at
// WIDTH×HEIGHT. The whole source image is fitted (never cropped); any
// leftover space is filled with a blurred, darkened copy of the image.
// Everything is re-encoded to WebP.
class Screenshot
{
    public const WIDTH = 1280;

    public const HEIGHT = 720;

    /**
     * Returns the re-encoded WebP binary, or null when GD (with WebP support)
     * is unavailable or the data cannot be decoded — callers keep the original
     * file in that case.
     */
    public static function normalize(string $binary): ?string
    {
        if (! function_exists('imagecreatefromstring') || ! function_exists('imagewebp')) {
            return null;
        }

        $source = @imagecreatefromstring($binary);

        if ($source === false) {
            return null;
        }

        $sourceWidth = imagesx($source);
        $sourceHeight = imagesy($source);

        $canvas = imagecreatetruecolor(self::WIDTH, self::HEIGHT);

        // Preserve transparency (e.g. PNGs with an alpha channel) instead of
        // flattening it onto the default opaque black: start from a fully
        // transparent canvas and keep alpha on save. Blending is turned back
        // on so the blurred backdrop and the fitted image composite normally.
        imagesavealpha($canvas, true);
        imagealphablending($canvas, false);
        $transparent = imagecolorallocatealpha($canvas, 0, 0, 0, 127);
        imagefill($canvas, 0, 0, $transparent);
        imagealphablending($canvas, true);

        // Fit the whole image inside the frame, upscaling small ones.
        $scale = min(self::WIDTH / $sourceWidth, self::HEIGHT / $sourceHeight);
        $fitWidth = max(1, (int) round($sourceWidth * $scale));
        $fitHeight = max(1, (int) round($sourceHeight * $scale));
        $fitX = (int) floor((self::WIDTH - $fitWidth) / 2);
        $fitY = (int) floor((self::HEIGHT - $fitHeight) / 2);

        if ($fitWidth < self::WIDTH || $fitHeight < self::HEIGHT) {
            self::paintBlurredBackdrop($canvas, $source, $sourceWidth, $sourceHeight);
        }

        imagecopyresampled($canvas, $source, $fitX, $fitY, 0, 0, $fitWidth, $fitHeight, $sourceWidth, $sourceHeight);
        imagedestroy($source);

        ob_start();

        $encoded = imagewebp($canvas, null, 85);

        $output = ob_get_clean();
        imagedestroy($canvas);

        return $encoded && $output !== false && $output !== '' ? $output : null;
    }

    /**
     * Builds a stored filename for a screenshot: the app's domain followed by
     * a unique number and the .webp extension — e.g. "example_com_202607...webp".
     */
    public static function filename(): string
    {
        $domain = Str::slug(parse_url((string) config('app.url'), PHP_URL_HOST) ?: 'app', '_');

        // Timestamp + random keeps it a unique "number"; a collision within a
        // single product folder is effectively impossible.
        $unique = now()->format('YmdHis') . random_int(1000, 9999);

        return $domain . '_' . $unique . '.webp';
    }

    /**
     * Fills the canvas with a heavily blurred, darkened cover-crop of the
     * source so fitted images sit on their own colors instead of flat bars.
     */
    private static function paintBlurredBackdrop(\GdImage $canvas, \GdImage $source, int $sourceWidth, int $sourceHeight): void
    {
        // Largest centered 16:9 window inside the source.
        $cropWidth = $sourceWidth;
        $cropHeight = (int) round($sourceWidth * self::HEIGHT / self::WIDTH);

        if ($cropHeight > $sourceHeight) {
            $cropHeight = $sourceHeight;
            $cropWidth = max(1, (int) round($sourceHeight * self::WIDTH / self::HEIGHT));
        }

        $cropHeight = max(1, $cropHeight);
        $cropX = (int) floor(($sourceWidth - $cropWidth) / 2);
        $cropY = (int) floor(($sourceHeight - $cropHeight) / 2);

        // Blur on small bitmaps where GD's 3×3 gaussian kernel is effectively
        // huge, upscaling in two stages so the interpolation stays smooth.
        $small = imagecreatetruecolor(64, 36);
        imagecopyresampled($small, $source, 0, 0, $cropX, $cropY, 64, 36, $cropWidth, $cropHeight);

        for ($i = 0; $i < 8; $i++) {
            imagefilter($small, IMG_FILTER_GAUSSIAN_BLUR);
        }

        $mid = imagecreatetruecolor(320, 180);
        imagecopyresampled($mid, $small, 0, 0, 0, 0, 320, 180, 64, 36);
        imagedestroy($small);

        for ($i = 0; $i < 4; $i++) {
            imagefilter($mid, IMG_FILTER_GAUSSIAN_BLUR);
        }

        imagefilter($mid, IMG_FILTER_BRIGHTNESS, -40);
        imagecopyresampled($canvas, $mid, 0, 0, 0, 0, self::WIDTH, self::HEIGHT, 320, 180);
        imagedestroy($mid);
    }
}
