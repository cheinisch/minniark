<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../../vendor/autoload.php';
use Symfony\Component\Yaml\Yaml;

$pluginDir = __DIR__;
$settingsFile = $pluginDir . '/settings.json';
if (!file_exists($settingsFile)) return [];

$settings = json_decode(file_get_contents($settingsFile), true);
if (empty($settings['enabled'])) return [];

$shortname = $settings['disqus_shortname'] ?? '';
$enabledGlobal = !empty($settings['enabled']);

// Slug aus URL ermitteln
$slug = null;
if (isset($_SERVER['REQUEST_URI']) && preg_match('#/blog/([\w\-]+)#', $_SERVER['REQUEST_URI'], $matches)) {
    $slug = $matches[1];
}

// Post-Daten laden
$enabledLocal = false;
if ($slug) {
    $yamlPath = __DIR__ . '/../../../userdata/content/essay/' . $slug . '/' . $slug . '.yml';
    
    
    if (file_exists($yamlPath)) {
        try {
            $yaml = \Symfony\Component\Yaml\Yaml::parseFile($yamlPath);
        } catch (Exception $e) {
            error_log("YAML ERROR: " . $e->getMessage());
        }
        error_log(print_r($yaml, true));
        $post = $yaml['essay'] ?? [];
        $enabledLocal = !empty($post['enable_disqus']);
    }
}

// Gesamter Status
$enabled = $enabledGlobal && $enabledLocal;

if($enabled)
{
    error_log("Ist aktiv");
}else{
    error_log("Ist inaktiv");
}

// HTML nur bereitstellen, wenn aktiviert
$html = '';
if ($enabled && $shortname !== '') {
    $html = <<<HTML
<div id="disqus_thread" class="w-full"></div>
<script>
    var disqus_config = function () {
        this.page.url = "{$data['current_url']}";
        this.page.identifier = 'blog/{$post['slug']}';
    };
    (function() {
        var d = document, s = d.createElement('script');
        s.src = 'https://{$shortname}.disqus.com/embed.js';
        s.setAttribute('data-timestamp', +new Date());
        (d.head || d.body).appendChild(s);
    })();
</script>
<noscript>Please enable JavaScript to view the comments powered by Disqus.</noscript>
HTML;
}

$result = [
    'disqus' => [
        'enabled' => $enabled,
        'disqus_shortname' => $shortname,
        'html' => $html
    ]
];

error_log(print_r($result, true));
return $result;