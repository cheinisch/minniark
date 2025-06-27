<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use Symfony\Component\Yaml\Yaml;

function getAlbumList(): array
{
    $albumDir = __DIR__ . '/../../userdata/content/album/';
    if (!is_dir($albumDir)) {
        return [];
    }

    $albums = [];

    foreach (glob($albumDir . '*.yml') as $file) {
        $slug = pathinfo($file, PATHINFO_FILENAME);
        $yaml = Yaml::parseFile($file);
        $album = $yaml['album'] ?? [];

        $albums[] = [
            'Slug' => $slug,
            'Name' => $album['name'] ?? '',
            'Description' => $album['description'] ?? '',
            'Password' => $album['password'] ?? '',
            'Images' => $album['images'] ?? [],
            'HeadImage' => $album['headImage'] ?? '',
        ];
    }

    return $albums;
}

function getGalleryAlbums(): array
{
    $albumDir = realpath(__DIR__ . '/../../userdata/content/album/');
    if (!$albumDir) return [];

    $albums = [];
    $parsedown = new Parsedown();

    foreach (glob($albumDir . '/*.yml') as $file) {
        $slug = basename($file, '.yml');
        $yaml = Yaml::parseFile($file);
        $album = $yaml['album'] ?? [];

        $mdFile = $albumDir . '/' . $slug . '.md';
        $description = file_exists($mdFile) ? file_get_contents($mdFile) : '';

        $url = '/album/'.$slug;

        $albums[] = [
            'slug' => $slug,
            'title' => $album['name'] ?? '',
            'description' => $parsedown->text($description),
            'cover' => get_cacheimage($album['headImage'] ?? ''),
            'url' => $url,
        ];
    }

    return $albums;
}

function readGalleryAlbum(string $slug, array $settings): array
{
    $albumDir = realpath(__DIR__ . '/../../userdata/content/album/');
    $albumPath = $albumDir . '/' . $slug . '.yml';
    $mdPath = $albumDir . '/' . $slug . '.md';

    if (!file_exists($albumPath)) {
        return [];
    }

    $yaml = Yaml::parseFile($albumPath);
    $album = $yaml['album'] ?? [];

    $parsedown = new Parsedown();
    $imageSize = $settings['image_size'] ?? 'M';

    $imageDir = realpath(__DIR__ . '/../../userdata/content/images/');
    $cacheDir = '/cache/images/';

    $imageList = [];

    foreach ($album['images'] ?? [] as $img) {
        $jsonFile = $imageDir . '/' . pathinfo($img, PATHINFO_FILENAME) . '.json';
        if (file_exists($jsonFile)) {
            $meta = json_decode(file_get_contents($jsonFile), true);
            if (json_last_error() === JSON_ERROR_NONE && !empty($meta['guid'])) {
                $guid = $meta['guid'];
                $cachedImagePath = $cacheDir . $guid . '_' . $imageSize . '.jpg';
                $imageUrl = '/i/' . rawurlencode($img);

                $imageList[] = [
                    'file' => $cachedImagePath,
                    'url'  => $imageUrl,
                    'title' => $meta['title'] ?? '',
                ];
            }
        }
    }

    // Cover
    $cover = null;
    $headImage = $album['headImage'] ?? '';
    if (!empty($headImage)) {
        $coverJson = $imageDir . '/' . pathinfo($headImage, PATHINFO_FILENAME) . '.json';
        if (file_exists($coverJson)) {
            $meta = json_decode(file_get_contents($coverJson), true);
            if (json_last_error() === JSON_ERROR_NONE && !empty($meta['guid'])) {
                $cover = $cacheDir . $meta['guid'] . '_' . $imageSize . '.jpg';
            }
        }
    }

    return [
        'slug' => $slug,
        'title' => $album['name'] ?? '',
        'description' => $parsedown->text(file_exists($mdPath) ? file_get_contents($mdPath) : ''),
        'images' => $imageList,
        'cover' => $cover,
    ];
}
