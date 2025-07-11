<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    // Hilfsfunktion: Slash in Unterstrich für Ordnernamen
    function normalizeThemeFolder(string $packageName): string
    {
        return str_replace('/', '_', strtolower(trim($packageName)));
    }

    function getInstalledTemplates(): array
    {
        $templateDir = __DIR__ . '/../../userdata/template';
        $templates = [];

        if (!is_dir($templateDir)) return $templates;

        foreach (scandir($templateDir) as $dir) {
            if ($dir === '.' || $dir === '..') continue;

            $fullPath = $templateDir . '/' . $dir;
            if (!is_dir($fullPath)) continue;

            $jsonPath = $fullPath . '/theme.json';
            $imagePath = $fullPath . '/image.png';
            $composerPath = $fullPath . '/composer.json';

            if (!file_exists($jsonPath) || !file_exists($imagePath)) continue;

            $jsonContent = file_get_contents($jsonPath);
            $decoded = json_decode($jsonContent, true);
            if (json_last_error() !== JSON_ERROR_NONE) continue;

            $template = (isset($decoded[0]) && is_array($decoded[0])) ? $decoded[0] : (is_array($decoded) ? $decoded : null);
            if (!$template || !isset($template['name'])) continue;

            $updateVersion = getPackagistVersionNumber($dir);
            $template['image'] = realpath($imagePath);
            $template['folder'] = $dir;
            $template['update'] = $updateVersion;
            $template['update_available'] = singleUpdateAvailable($template['version'] ?? '', $updateVersion);

            $templates[] = $template;
        }

        return $templates;
    }

    function getPackagistVersionNumber(string $themefolder)
    {
        $composerfile = __DIR__ . '/../../userdata/template/' . $themefolder . '/composer.json';
        if (!file_exists($composerfile)) return 0;

        $composerData = json_decode(file_get_contents($composerfile), true);
        if (json_last_error() !== JSON_ERROR_NONE || !isset($composerData['name'])) return 0;

        $packageName = $composerData['name'];
        $url = "https://repo.packagist.org/p2/" . urlencode($packageName) . ".json";

        $context = stream_context_create([
            "http" => ["method" => "GET", "header" => "User-Agent: Minniark Theme Checker"]
        ]);

        $json = @file_get_contents($url, false, $context);
        if (!$json) return 0;

        $data = json_decode($json, true);
        $versions = $data['packages'][$packageName] ?? [];

        $versionNumbers = array_filter(array_column($versions, 'version'), function ($v) {
            return preg_match('/^\d+\.\d+(\.\d+)?$/', $v);
        });

        if (empty($versionNumbers)) return 0;

        usort($versionNumbers, 'version_compare');
        return end($versionNumbers);
    }

    function singleUpdateAvailable($oldVersion, $newVersion): bool
    {
        if ($newVersion === 0 || $newVersion === '0' || empty($oldVersion)) return false;
        return version_compare($newVersion, $oldVersion, '>');
    }

    function updateSingleTheme(string $foldername): bool
    {
        $templateDir = __DIR__ . '/../../userdata/template';
        $themePath = $templateDir . '/' . $foldername;
        $composerFile = $themePath . '/composer.json';

        if (!file_exists($composerFile)) return false;

        $composerData = json_decode(file_get_contents($composerFile), true);
        if (json_last_error() !== JSON_ERROR_NONE || !isset($composerData['name'])) return false;

        $packageName = $composerData['name'];
        $normalizedFolder = normalizeThemeFolder($packageName);
        $expectedPath = $templateDir . '/' . $normalizedFolder;

        if ($normalizedFolder !== $foldername) {
            if (!rename($themePath, $expectedPath)) return false;
            $themePath = $expectedPath;
        }

        $url = 'https://repo.packagist.org/p2/' . urlencode($packageName) . '.json';
        $context = stream_context_create([
            'http' => ['method' => 'GET', 'header' => "User-Agent: Minniark-Updater\r\n"]
        ]);

        $json = @file_get_contents($url, false, $context);
        if (!$json) return false;

        $data = json_decode($json, true);
        $versions = $data['packages'][$packageName] ?? [];
        if (empty($versions)) return false;

        usort($versions, fn($a, $b) => version_compare($a['version'], $b['version']));
        $latest = end($versions);
        $zipUrl = $latest['dist']['url'] ?? null;
        if (!$zipUrl) return false;

        $zipData = file_get_contents($zipUrl, false, $context);
        if (!$zipData) return false;

        $tempZip = tempnam(sys_get_temp_dir(), 'minniark_theme_') . '.zip';
        file_put_contents($tempZip, $zipData);

        $tempExtract = sys_get_temp_dir() . '/minniark_extract_' . uniqid();
        mkdir($tempExtract, 0777, true);

        $zip = new ZipArchive();
        if ($zip->open($tempZip) !== true) return false;
        $zip->extractTo($tempExtract);
        $zip->close();
        unlink($tempZip);

        $subdirs = array_filter(glob($tempExtract . '/*'), 'is_dir');
        $sourcePath = reset($subdirs);
        if (!is_dir($sourcePath)) return false;

        $backupPath = $themePath . '_backup_' . date('Ymd_His');
        if (!rename($themePath, $backupPath)) return false;

        if (!copyDir($sourcePath, $themePath)) {
            rename($backupPath, $themePath);
            return false;
        }

        deleteDir($tempExtract);
        deleteDir($backupPath); // oder behalten

        return true;
    }

    function copyDir(string $src, string $dst): bool
    {
        if (!is_dir($src)) return false;
        @mkdir($dst, 0777, true);

        foreach (scandir($src) as $item) {
            if ($item === '.' || $item === '..') continue;
            $srcPath = $src . '/' . $item;
            $dstPath = $dst . '/' . $item;
            is_dir($srcPath) ? copyDir($srcPath, $dstPath) : copy($srcPath, $dstPath);
        }

        return true;
    }

    function deleteDir(string $dir): void
    {
        if (!is_dir($dir)) return;
        foreach (scandir($dir) as $item) {
            if ($item === '.' || $item === '..') continue;
            $path = $dir . '/' . $item;
            is_dir($path) ? deleteDir($path) : unlink($path);
        }
        rmdir($dir);
    }

    function checkAllThemes(): bool
    {
        foreach (getInstalledTemplates() as $theme) {
            if (!empty($theme['update_available'])) {
                return true;
            }
        }
        return false;
    }

    function createThemeUpdateButton(): string
    {
        if (!checkAllThemes()) return '';

        return '
            <a href="dashboard-theme.php" class="relative inline-flex items-center gap-x-1.5 bg-sky-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-sky-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-600">
                <svg class="-ml-0.5 w-5 h-5" viewBox="0 0 25 25" fill="currentColor" aria-hidden="true">
                    <path d="M12.5535 16.5061C12.4114 16.6615 12.2106 16.75 12 16.75C11.7894 16.75 11.5886 16.6615 11.4465 16.5061L7.44648 12.1311C7.16698 11.8254 7.18822 11.351 7.49392 11.0715C7.79963 10.792 8.27402 10.8132 8.55352 11.1189L11.25 14.0682V3C11.25 2.58579 11.5858 2.25 12 2.25C12.4142 2.25 12.75 2.58579 12.75 3V14.0682L15.4465 11.1189C15.726 10.8132 16.2004 10.792 16.5061 11.0715C16.8118 11.351 16.833 11.8254 16.5535 12.1311L12.5535 16.5061Z"/>
                    <path d="M3.75 15C3.75 14.5858 3.41422 14.25 3 14.25C2.58579 14.25 2.25 14.5858 2.25 15V15.0549C2.24998 16.4225 2.24996 17.5248 2.36652 18.3918C2.48754 19.2919 2.74643 20.0497 3.34835 20.6516C3.95027 21.2536 4.70814 21.5125 5.60825 21.6335C6.47522 21.75 7.57754 21.75 8.94513 21.75H15.0549C16.4225 21.75 17.5248 21.75 18.3918 21.6335C19.2919 21.5125 20.0497 21.2536 20.6517 20.6516C21.2536 20.0497 21.5125 19.2919 21.6335 18.3918C21.75 17.5248 21.75 16.4225 21.75 15.0549V15C21.75 14.5858 21.4142 14.25 21 14.25C20.5858 14.25 20.25 14.5858 20.25 15C20.25 16.4354 20.2484 17.4365 20.1469 18.1919C20.0482 18.9257 19.8678 19.3142 19.591 19.591C19.3142 19.8678 18.9257 20.0482 18.1919 20.1469C17.4365 20.2484 16.4354 20.25 15 20.25H9C7.56459 20.25 6.56347 20.2484 5.80812 20.1469C5.07435 20.0482 4.68577 19.8678 4.40901 19.591C4.13225 19.3142 3.9518 18.9257 3.85315 18.1919C3.75159 17.4365 3.75 16.4354 3.75 15Z"/>
                </svg>
                Theme Updates available
            </a>';
    }

    function installTemplate($packagistName)
    {
        $templateDir = __DIR__ . '/../../userdata/template';
        $normalizedFolder = normalizeThemeFolder($packagistName);
        $themePath = $templateDir . '/' . $normalizedFolder;

        if (is_dir($themePath)) {
            // Bereits installiert
            return false;
        }

        $url = 'https://repo.packagist.org/p2/' . urlencode($packagistName) . '.json';
        $context = stream_context_create([
            'http' => ['method' => 'GET', 'header' => "User-Agent: Minniark-Installer\r\n"]
        ]);

        $json = @file_get_contents($url, false, $context);
        if (!$json) return false;

        $data = json_decode($json, true);
        if (json_last_error() !== JSON_ERROR_NONE || empty($data['packages'][$packagistName])) {
            return false;
        }

        $versions = $data['packages'][$packagistName];

        // Finde die neueste stabile Version
        $stableVersions = array_filter($versions, fn($v) => preg_match('/^\d+\.\d+(\.\d+)?$/', $v['version']));
        if (empty($stableVersions)) return false;

        usort($stableVersions, fn($a, $b) => version_compare($a['version'], $b['version']));
        $latest = end($stableVersions);
        $zipUrl = $latest['dist']['url'] ?? null;
        if (!$zipUrl) return false;

        $zipData = @file_get_contents($zipUrl, false, $context);
        if (!$zipData) return false;

        // Temporäres ZIP speichern
        $tempZip = tempnam(sys_get_temp_dir(), 'minniark_install_') . '.zip';
        file_put_contents($tempZip, $zipData);

        // Entpacken
        $tempExtract = sys_get_temp_dir() . '/minniark_extract_' . uniqid();
        mkdir($tempExtract, 0777, true);

        $zip = new ZipArchive();
        if ($zip->open($tempZip) !== true) return false;
        $zip->extractTo($tempExtract);
        $zip->close();
        unlink($tempZip);

        // Finde das entpackte Root-Verzeichnis
        $subdirs = array_filter(glob($tempExtract . '/*'), 'is_dir');
        $sourcePath = reset($subdirs);
        if (!is_dir($sourcePath)) return false;

        // Zielverzeichnis anlegen und kopieren
        mkdir($themePath, 0777, true);
        if (!copyDir($sourcePath, $themePath)) {
            deleteDir($themePath);
            return false;
        }

        // Aufräumen
        deleteDir($tempExtract);

        return true;
    }


    function getTemplatesPackagist():array
    {
        $query = 'minniark-template';
        $perPage = 6;
        $url = sprintf(
            'https://packagist.org/search.json?q=%s&per_page=%d',
            urlencode($query),
            $perPage
        );
        $ctx = stream_context_create(['http' => ['header' => 'User-Agent: Minniark Theme Loader']]);
        $json = @file_get_contents($url, false, $ctx);
        $data = $json ? json_decode($json, true) : null;
        $themes = [];

        if (!empty($data['results'])) {
            foreach ($data['results'] as $pkg) {
                $name = $pkg['name'];
                $detailUrl = "https://packagist.org/packages/{$name}.json";
                $detailJson = @file_get_contents($detailUrl, false, $ctx);
                $detailData = $detailJson ? json_decode($detailJson, true) : null;

                // Hole die stabilste Version (ohne dev, beta, RC)
                $version = 'unknown';
                if (
                    !empty($detailData['package']['versions']) &&
                    is_array($detailData['package']['versions'])
                ) {
                    $stableVersions = array_filter(
                        array_keys($detailData['package']['versions']),
                        fn($v) => preg_match('/^\d+\.\d+(\.\d+)?$/', $v)
                    );
                    if ($stableVersions) {
                        usort($stableVersions, 'version_compare');
                        $version = end($stableVersions);
                    }
                }

                $versionData = $detailData['package']['versions'][$version] ?? null;

                $author = 'Unknown';
                if (!empty($versionData['authors']) && is_array($versionData['authors'])) {
                    // Nimm den ersten Author-Namen, wenn vorhanden
                    $author = $versionData['authors'][0]['name'] ?? 'Unknown';
                }

                $themes[] = [
                    'name'        => $name,
                    'author'	  => $author,
                    'version'     => $version,
                    'description' => $pkg['description'] ?? '',
                    'url'         => $pkg['url'],
                    'image'       => 'https://picsum.photos/300/200'
                ];
            }
        }

        return $themes;
    }


    function removeTheme(string $foldername): bool
    {
        $templateDir = __DIR__ . '/../../userdata/template';
        $themePath = $templateDir . '/' . $foldername;

        // Existiert der Ordner?
        if (!is_dir($themePath)) {
            return false;
        }

        // Versuche, den Ordner rekursiv zu löschen
        try {
            deleteDir($themePath);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }