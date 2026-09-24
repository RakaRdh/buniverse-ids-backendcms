<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Libraries\ImageOptimizer;

class OptimizeExistingImages extends BaseCommand
{
    protected $group       = 'Maintenance';
    protected $name        = 'images:optimize';
    protected $description = 'Safely resizes oversized images in uploads/ to prevent slow page load without quality degradation.';

    public function run(array $params)
    {
        CLI::write("Scanning BackendCMS uploads/ directory for oversized images...", 'yellow');
        $uploadsDir = FCPATH . 'uploads';

        if (!is_dir($uploadsDir)) {
            CLI::error("Uploads directory not found: {$uploadsDir}");
            return;
        }

        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($uploadsDir));
        $count = 0;
        $savedBytes = 0;

        foreach ($iterator as $file) {
            if ($file->isDir()) continue;
            $path = $file->getPathname();
            $ext  = strtolower(pathinfo($path, PATHINFO_EXTENSION));

            if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                continue;
            }

            $sizeBefore = filesize($path);
            if ($sizeBefore < 300 * 1024) {
                // Already under 300 KB, skip
                continue;
            }

            // Determine max dimensions based on path
            $maxW = 1600;
            $maxH = 1600;
            if (str_contains($path, 'speakers') || str_contains($path, 'moderators') || str_contains($path, 'pengantar')) {
                $maxW = 800;
                $maxH = 800;
            }

            $ok = ImageOptimizer::optimize($path, $maxW, $maxH, 85);
            if ($ok) {
                clearstatcache(true, $path);
                $sizeAfter = filesize($path);
                $saved = $sizeBefore - $sizeAfter;
                if ($saved > 0) {
                    $savedBytes += $saved;
                    $count++;
                    $rel = str_replace(FCPATH, '', $path);
                    CLI::write(sprintf("Optimized: %s (%s -> %s)", $rel, round($sizeBefore/1024, 1) . ' KB', round($sizeAfter/1024, 1) . ' KB'), 'green');
                }
            }
        }

        CLI::write(sprintf("Completed! Optimized %d file(s), saved total %s MB.", $count, round($savedBytes / (1024 * 1024), 2)), 'cyan');
    }
}
