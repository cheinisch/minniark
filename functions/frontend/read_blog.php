<?php

    require_once __DIR__ . '/../../vendor/autoload.php'; // für Yaml
    use Symfony\Component\Yaml\Yaml;

    function hasBlogPosts()
    {
        $hasPosts = false;

        $postDir = __DIR__ . '/../../userdata/content/essay/';

        $folderCount = count_subfolders($postDir);

        if($folderCount > 0)
        {
            $hasPosts = true;
        }

        return $hasPosts;
    }

    function count_posts()
    {
        $postDir = __DIR__ . '/../../userdata/content/essay/';

        $folderCount = count_subfolders($postDir);


        return $folderCount;
    }


    function count_subfolders($postDir)
    {
        if (!is_dir($postDir)) {
            return 0;
        }
    
        $items = scandir($postDir);
    
        $folders = array_filter($items, function ($item) use ($postDir) {
            if (!is_string($item) || trim($item) === '.' || trim($item) === '..') {
                return false; // nur OK im Filter-Callback
            }
        
            $path = rtrim($postDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $item;
        
            $isDir = is_dir($path);
        
            return $isDir;
        });
    
        return count($folders);
    }

    

function getBlogPosts(): array
{
    $baseDir = realpath(__DIR__ . '/../../userdata/content/essay/');
    if (!$baseDir) {
        return [];
    }

    $posts = [];

    foreach (scandir($baseDir) as $folder) {
        if ($folder === '.' || $folder === '..') continue;

        if (!preg_match('/^[a-z0-9\-]+$/', $folder)) continue;

        $slug = $folder;
        $folderPath = $baseDir . '/' . $slug;
        $yamlPath = $folderPath . '/' . $slug . '.yml';
        $mdPath = $folderPath . '/' . $slug . '.md';

        if (!file_exists($yamlPath) || !file_exists($mdPath)) {
            continue;
        }

        $yaml = Yaml::parseFile($yamlPath);
        $essay = $yaml['essay'] ?? [];

        $parsedown = new Parsedown();

        // Fallbacks
        $title = $essay['title'] ?? ucfirst($slug);
        $created = $essay['created_at'] ?? '1970-01-01';
        $rawContent = file_get_contents($mdPath);
        $excerpt =  $parsedown->text(mb_substr(strip_tags($rawContent), 0, 150) . '...');

        $url = '/blog/'.$slug;

        if($essay['is_published'] == false)
        {
            continue;
        }

        $posts[] = [
            'slug' => $slug,
            'title' => $title,
            'date' => $created,
            'excerpt' => $excerpt,
            'content' => $excerpt,
            'cover' => get_cacheimage($essay['cover'] ?? ''),
            'is_published' => $essay['is_published'] ?? false,
            'tags' => $essay['tags'] ?? [],
            'url' => $url,
        ];
    }

    // Sortieren nach Datum absteigend
    usort($posts, fn($a, $b) => strcmp($b['date'], $a['date']));

    return $posts;
}

