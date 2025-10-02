<?php
require_once(__DIR__ . '/../../vendor/autoload.php');

use Symfony\Component\Yaml\Yaml;

function save_navigation(array $nav, bool $debug = false): bool
{
    // Zielpfad
    $dir  = __DIR__ . '/../../userdata/config';
    $file = $dir . '/navigation.yml';

    // Debug-Helper
    $log = function (string $label, $val = null) use ($debug) {
        if (!$debug) return;
        echo "---- {$label} ----\n";
        if (is_array($val) || is_object($val)) {
            print_r($val);
        } else {
            var_dump($val);
        }
        echo "\n";
    };

    try {
        // 1) Eingabe validieren
        if (!is_array($nav)) {
            error_log("save_navigation: nav must be array");
            $log('ERROR', 'nav is not array');
            return false;
        }

        // 2) Zielordner sicherstellen
        if (!is_dir($dir)) {
            if (!@mkdir($dir, 0775, true) && !is_dir($dir)) {
                error_log("save_navigation: failed to create dir {$dir}");
                $log('MKDIR FAILED', $dir);
                return false;
            }
        }

        // 3) Schreibrechte prüfen
        if (!is_writable($dir)) {
            error_log("save_navigation: dir not writable {$dir}");
            $log('DIR NOT WRITABLE', $dir);
            return false;
        }

        // 4) YAML generieren – Tiefe höher für verschachtelte Menüs
        // Yaml::dump($value, $inline, $indent)
        // $inline = 8 (Tiefe), $indent = 4
        $yaml = Yaml::dump($nav, 8, 4);
        $log('YAML (to write)', $yaml);

        // 5) Atomar schreiben (Lock) + Fehler prüfen
        $bytes = @file_put_contents($file, $yaml, LOCK_EX);
        if ($bytes === false) {
            $err = error_get_last();
            error_log("save_navigation: file_put_contents failed for {$file}: " . ($err['message'] ?? 'unknown'));
            $log('WRITE FAILED', $err);
            return false;
        }
        $log('BYTES WRITTEN', $bytes);

        // 6) Rücklesen & vergleichen (Integrity Check)
        $readBack = @file_get_contents($file);
        if ($readBack === false) {
            error_log("save_navigation: could not read back {$file}");
            $log('READBACK FAILED');
            return false;
        }
        if ($readBack !== $yaml) {
            error_log("save_navigation: readback mismatch for {$file}");
            $log('READBACK MISMATCH', ['expected' => $yaml, 'actual' => $readBack]);
            return false;
        }

        // 7) Pfadinformationen loggen
        $log('FILE PATH', realpath($file));
        $log('PERMISSIONS', substr(sprintf('%o', fileperms($file)), -4));

        return true;
    } catch (\Throwable $e) { // fängt auch TypeError etc.
        error_log("save_navigation: exception: " . $e->getMessage());
        $log('EXCEPTION', $e->getMessage());
        return false;
    }
}

/**
 * Liest die Navigation aus userdata/config/navigation.yml
 * @param bool $debug  Wenn true, werden Diagnose-Infos direkt ausgegeben.
 * @return array       Normalisierte Navigationsstruktur (Liste von Items)
 */
function read_navigation(bool $debug = false): array
{
    $dir  = __DIR__ . '/../../userdata/config';
    $file = $dir . '/navigation.yml';

    $log = function (string $label, $val = null) use ($debug) {
        if (!$debug) return;
        echo "---- {$label} ----\n";
        if (is_array($val) || is_object($val)) {
            print_r($val);
        } else {
            var_dump($val);
        }
        echo "\n";
    };

    // 1) Existenz/Lesbarkeit prüfen
    $log('FILE (expected path)', $file);
    if (!file_exists($file)) {
        $log('FILE EXISTS', false);
        return [];
    }
    $log('FILE EXISTS', true);
    $log('REALPATH', realpath($file));
    $log('PERMISSIONS', @substr(sprintf('%o', @fileperms($file)), -4));
    if (!is_readable($file)) {
        $log('READABLE', false);
        error_log("read_navigation: file not readable: {$file}");
        return [];
    }
    $log('READABLE', true);

    // 2) Rohinhalt (für Debug)
    $raw = @file_get_contents($file);
    $log('RAW CONTENT (first 500 chars)', mb_substr($raw ?? '', 0, 500));
    $log('RAW LENGTH', is_string($raw) ? strlen($raw) : null);

    if ($raw === false || trim($raw) === '') {
        $log('EMPTY FILE or read error');
        return [];
    }

    // 3) YAML parsen
    try {
        // Parse statt parseFile, damit wir vorher raw debuggen konnten
        $data = Yaml::parse($raw);

        // Manche Setups liefern null bei leerem YAML
        if ($data === null) {
            $log('PARSED DATA', 'null');
            return [];
        }

        if (!is_array($data)) {
            $log('PARSED TYPE', gettype($data));
            $log('PARSED VALUE', $data);
            // Wir erwarten eine Liste von Items oder ein Mapping – alles andere leeren wir defensiv
            return [];
        }

        // 4) Normalisieren/validieren: Liste von Items (label/link/children)
        $normalized = normalize_nav($data, $log);
        $log('NORMALIZED NAV', $normalized);

        return $normalized;
    } catch (ParseException $e) {
        $log('YAML PARSE EXCEPTION', $e->getMessage());
        error_log("read_navigation: YAML parse error: " . $e->getMessage());
        return [];
    } catch (\Throwable $e) {
        $log('THROWABLE', $e->getMessage());
        error_log("read_navigation: exception: " . $e->getMessage());
        return [];
    }
}

/**
 * Normalisiert eingelesene YAML-Struktur auf eine Liste von Items
 * Jedes Item: ['label' => string, 'link' => string, 'children' => array?]
 */
function normalize_nav($data, callable $log): array
{
    // YAML kann entweder eine Liste sein:
    // - {label: Home, link: /home}
    // oder ein Mapping auf "items": [...]
    if (isset($data['items']) && is_array($data['items'])) {
        $data = $data['items'];
    }

    // Falls top-level kein numerisch indiziertes Array ist, in Liste umwandeln
    if (!is_array($data) || array_values($data) === []) {
        // z.B. Einzelnes Mapping -> in Liste packen
        if (isset($data['label']) || isset($data['link'])) {
            $data = [$data];
        }
    }

    $result = [];
    foreach ((array)$data as $idx => $item) {
        if (!is_array($item)) {
            $log('SKIP NON-ARRAY ITEM', [$idx => $item]);
            continue;
        }
        $label = isset($item['label']) ? (string)$item['label'] : '';
        $link  = isset($item['link'])  ? (string)$item['link']  : '';

        if ($label === '' || $link === '') {
            $log('SKIP INVALID ITEM (missing label/link)', $item);
            continue;
        }

        $out = ['label' => $label, 'link' => $link];

        if (isset($item['children']) && is_array($item['children'])) {
            $out['children'] = normalize_nav($item['children'], $log);
        }

        $result[] = $out;
    }

    return $result;
}