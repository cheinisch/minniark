<?php

    require_once(__DIR__ . '/../../vendor/autoload.php');
    use Symfony\Component\Yaml\Yaml;

    /**
     * Übersetzungsfunktion
     *
     * @param string   $key    z.B. "nav.dashboard"
     * @param array    $vars   Platzhalter, z.B. ["name" => "Chris"]
     * @param int|null $count  optional für einfache Plurale (_0/_1/_n)
     * @return string
     */
    function languageString(string $key, array $vars = [], ?int $count = null): string
    {
        static $cache = []; // ["lang" => [...katalog...]]
        $base = dirname(__DIR__, 2); // .../ (passt zu deinem __DIR__.'/../../')
        $settingsPath = $base . '/userdata/config/settings.yml';

        // 1) Sprache aus YAML lesen (oder Default)
        $lang = 'en-US';
        if (is_file($settingsPath)) {
            try {
                $settingsArray = Yaml::parseFile($settingsPath);
                if (!empty($settingsArray['language']) && is_string($settingsArray['language'])) {
                    $lang = $settingsArray['language'];
                }
            } catch (\Throwable $e) {
                error_log("YAML Parse Error: " . $e->getMessage());
            }
        }

        // 2) Katalog laden (mit Fallback-Kette, z.B. de-DE -> de -> en)
        $fallbacks = array_unique([
            $lang,                     // z.B. de-DE
            substr($lang, 0, 2),       // z.B. de
            'en-US',                   // harter Fallback
            'en'                       // ganz zum Schluss generisches en
        ]);
        $catalog = [];
        foreach ($fallbacks as $lc) {
            if (!isset($cache[$lc])) {
                $file = $base . '/language/' . $lc . '.json';
                $cache[$lc] = is_file($file)
                    ? (json_decode(file_get_contents($file), true) ?: [])
                    : [];
            }
            // mergen: erst vorhandenes, dann Fallback drunter
            $catalog = array_replace_recursive($cache[$lc], $catalog);
        }

        // 3) Wert per Dot-Key holen (unterstützt verschachtelte Objekte)
        $raw = dotGet($catalog, $key);
        if ($raw === null) {
            // Key nicht gefunden -> gib den Key selbst zurück (sichtbarer Hinweis)
            return $key;
        }

        // 4) Einfache Pluralregeln mit _0/_1/_n
        if (is_array($raw)) {
            $msg = ($count === 0 && isset($raw['_0'])) ? $raw['_0']
                : (($count === 1 && isset($raw['_1'])) ? $raw['_1']
                : ($raw['_n'] ?? ''));
        } else {
            $msg = (string)$raw;
        }

        // 5) Interpolation {{var}}
        if ($vars || $count !== null) {
            $varsAll = $vars;
            if ($count !== null) $varsAll['count'] = $count;
            $msg = preg_replace_callback('/\{\{(\w+)\}\}/', function($m) use ($varsAll) {
                return array_key_exists($m[1], $varsAll) ? (string)$varsAll[$m[1]] : $m[0];
            }, $msg);
        }

        return $msg;
    }

    /** Helper: Dot-Access */
    function dotGet(array $arr, string $path)
    {
        // unterstützt auch flache Keys, falls du "nav.dashboard" direkt als Key speicherst
        if (array_key_exists($path, $arr)) return $arr[$path];
        $cur = $arr;
        foreach (explode('.', $path) as $seg) {
            if (!is_array($cur) || !array_key_exists($seg, $cur)) return null;
            $cur = $cur[$seg];
        }
        return $cur;
    }


    /**
     * Liefert die verfügbaren Sprachdateien (Basename ohne .json) als Array zurück.
     * Standardpfad: <projektwurzel>/language/*.json
     *
     * @param string|null $dir Optionaler Ordnerpfad; Standard ist .../language
     * @return array<string>
     */
    function getLangFiles(?string $dir = null): array
    {
        // Standard: zwei Ebenen hoch ab aktuellem File, dann /language
        $base = dirname(__DIR__, 2);
        $dir  = $dir ?? ($base . '/language');

        if (!is_dir($dir)) {
            return [];
        }

        // Alle .json-Dateien einsammeln (case-insensitive via GLOB_BRACE nicht überall verfügbar, daher doppelt)
        $files = array_merge(
            glob($dir . '/*.json') ?: [],
            glob($dir . '/*.JSON') ?: []
        );

        // In Basenames ohne Extension umwandeln, versteckte/sonderfälle filtern, Duplikate entfernen
        $names = [];
        foreach ($files as $path) {
            if (!is_file($path)) continue;
            $base = basename($path);
            if ($base[0] === '.') continue; // ignore .hidden.json
            $name = pathinfo($base, PATHINFO_FILENAME);
            if ($name !== '') $names[strtolower($name)] = $name; // de-dupe case-insensitiv
        }

        // Sortieren (natürlich, case-insensitiv) und als Liste zurückgeben
        $out = array_values($names);
        natcasesort($out);
        return array_values($out);
    }
