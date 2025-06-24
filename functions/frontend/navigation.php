<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require_once(__DIR__ . '/../../vendor/autoload.php');

    use Symfony\Component\Yaml\Yaml;

    function read_navigation()
    {
        $file = __DIR__ . '/../../userdata/config/navigation.yml';

        if (!file_exists($file)) {
            return []; // Datei existiert nicht → leeres Menü
        }

        try {
            $data = Yaml::parseFile($file);
            return is_array($data) ? $data : [];
        } catch (Exception $e) {
            error_log("Failed to read YAML: " . $e->getMessage());
            return [];
        }
    }

    function buildNavigation(string $templateDir): array
    {
        $nav = [];

        if(is_nav_enabled())
        {
            $customNav = read_navigation();

            foreach ($customNav as $item) {
                $nav[] = convertCustomNavItem($item);
            }
        }else{
            $timelineEnable = is_timeline_enabled();
            $mapEnable = is_map_enabled();

            // Prüfe, ob map.twig existiert
            $hasMap = file_exists($templateDir . '/map.twig');

            // Prüfe, ob timeline.twig existiert
            $hasTimeline = file_exists($templateDir . '/timeline.twig');

            // Prüfe, ob blog.twig existiert
            $hasBlog = file_exists($templateDir . '/blog.twig');
            $hasBlogItems = hasBlogPosts();

            $hasCollection = file_exists($templateDir . '/collection.list.twig');
            $hasCollectionItems = hasCollections();

            $albumList = null;
            $albumList = getAlbumList();

            $pageList = null;
            $pageList = getPageList();
            // Home ist immer dabei
            $nav[] = ['title' => 'Home', 'url' => '/home'];

            if(!empty($albumList))
            {
                $children = [];

                foreach ($albumList as $album) {
                    $children[] = [
                        'title' => $album['Name'],
                        'url' => '/gallery/' . $album['Slug'], // oder 'id', je nachdem
                    ];
                }

                $nav[] = [
                    'title' => 'Gallerys',
                    'url' => '/gallery',
                    'children' => $children,
                ];
            }else{
                $nav[] = ['title' => 'Gallerys', 'url' => '/gallery'];
            }
            

            // Nur hinzufügen, wenn Template existiert
            if ($hasMap && $mapEnable) {
                $nav[] = ['title' => 'Map', 'url' => '/map'];
            }

            if ($hasTimeline && $timelineEnable) {
                $nav[] = ['title' => 'Timeline', 'url' => '/timeline'];
            }

            if ($hasBlog && $hasBlogItems) {
                $nav[] = ['title' => 'Blog', 'url' => '/blog'];
            }

            if ($hasCollection && $hasCollectionItems) {
                $nav[] = ['title' => 'Collections', 'url' => '/collections'];
            }

            if(!empty($pageList))
            {
                foreach ($pageList as $page) {
                    $nav[] = ['title' => $page['title'], 'url' => '/p/'.$page['slug']];
                }
            }
        }


        return $nav;
    }


    function convertCustomNavItem(array $item): array
{
    $converted = [
        'title' => $item['label'],
        'url'   => $item['link'],
    ];

    if (!empty($item['children']) && is_array($item['children'])) {
        $converted['children'] = array_map('convertCustomNavItem', $item['children']);
    }

    return $converted;
}
