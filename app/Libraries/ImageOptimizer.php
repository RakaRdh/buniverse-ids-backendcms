<?php

namespace App\Libraries;

use Config\Services;

class ImageOptimizer
{
    /**
     * Resize and optimize an image file in-place if it exceeds max dimension.
     * Uses CodeIgniter's GD/Imagick service.
     * Keeps quality high (default 85) and target max dimensions safe (default 1600px).
     *
     * @param string $filePath Full filesystem path to image
     * @param int $maxWidth Max allowed width (default 1600)
     * @param int $maxHeight Max allowed height (default 1600)
     * @param int $quality Compression quality (default 85)
     * @return bool True if processed/optimized, false if skipped or failed
     */
    public static function optimize(string $filePath, int $maxWidth = 1600, int $maxHeight = 1600, int $quality = 85): bool
    {
        if (!file_exists($filePath) || !is_file($filePath)) {
            return false;
        }

        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
            return false;
        }

        // Dynamically ensure sufficient memory for GD uncompressed pixel buffer (raw image decoded into RAM)
        $currentLimit = ini_get('memory_limit');
        @ini_set('memory_limit', '512M');

        // Check current dimensions
        $info = @getimagesize($filePath);
        if (!$info) {
            return false;
        }

        $width = $info[0];
        $height = $info[1];

        // Need resize if width > maxWidth or height > maxHeight
        $needsResize = ($width > $maxWidth || $height > $maxHeight);
        // Also if file size > 500 KB, re-saving with quality 85 brings it down safely
        $fileSize = filesize($filePath);
        $needsCompress = ($fileSize > 500 * 1024);

        if (!$needsResize && !$needsCompress) {
            return false;
        }

        try {
            $imageService = Services::image();
            $handler = $imageService->withFile($filePath);

            if ($needsResize) {
                $handler->resize($maxWidth, $maxHeight, true, 'auto');
            }

            // Save back to same file path with quality
            $handler->save($filePath, $quality);
            return true;
        } catch (\Throwable $e) {
            log_message('error', 'ImageOptimizer error on ' . $filePath . ': ' . $e->getMessage());
            return false;
        }
    }
}
