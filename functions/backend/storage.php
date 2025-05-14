<?php

    function getStorage(): array
    {
        $projectRoot = __DIR__ . '/../../userdata/';
        $total = disk_total_space($projectRoot);
        $free = disk_free_space($projectRoot);

        $usedDir = __DIR__ . '/../../';

        $used = getDirectorySize($usedDir);

        $cacheDir = __DIR__ . '/../../cache/';
        $imageDir = __DIR__ . '/../../userdata/content/images/';

        $imageUsage = getDirectorySize($imageDir);
        $cacheUsage = getDirectorySize($cacheDir);
        $rest = $used - $imageUsage - $cacheUsage;

        return [
            'total'  => bytesToMB($total),
            'used'   => bytesToMB($used),
            'free'   => bytesToMB($free),
            'images' => bytesToMB($imageUsage),
            'cache'  => bytesToMB($cacheUsage),
            'rest'   => bytesToMB($rest),
        ];
    }

    /**
     * Rekursive Ordnergrößenberechnung
     */
    function getDirectorySize(string $dir): int
    {
        $size = 0;
        if (!is_dir($dir)) {
            return 0;
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $size += $file->getSize();
            }
        }

        return $size;
    }

    function bytesToMB(int $bytes, int $precision = 1): float
    {
        return round($bytes / 1024 / 1024, $precision);
    }
