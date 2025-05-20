<?php

require_once(__DIR__ . "/../../vendor/autoload.php");

use Symfony\Component\Yaml\Yaml;

function getImage($imagename)
{
    $fileName = $imagename;
    $yamlFiles = glob("../userdata/content/images/*.yml");

    foreach ($yamlFiles as $file) {
        $image = Yaml::parseFile($file);
        if ($image && ($image['filename'] ?? null) === $fileName) {
            return $image;
        }
    }
    return null;
}

function count_images()
{
    $imageDir = '../userdata/content/images/';
    $files = glob($imageDir . '*.yml');
    echo count($files);
}

function get_imageyearlist($mobile)
{
    $imageDir = '../userdata/content/images/';
    $yearCounts = [];
    foreach (glob($imageDir . '*.yml') as $filePath) {
        $data = Yaml::parseFile($filePath);
        $date = $data['exif']['Date'] ?? null;
        if ($date) {
            $year = substr($date, 0, 4);
            if (ctype_digit($year)) {
                $yearCounts[$year] = ($yearCounts[$year] ?? 0) + 1;
            }
        }
    }
    ksort($yearCounts);
    foreach ($yearCounts as $year => $count) {
        $html = $mobile
            ? "<div class=\"pl-5\"><a href=\"media.php?year=$year\" class=\"block px-4 text-base font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-800 sm:px-6\">$year ($count)</a></div>"
            : "<li><a href=\"media.php?year=$year\" class=\"text-gray-400 hover:text-sky-400\">$year ($count)</a></li>";
        echo $html . "\n";
    }
}

function get_ratinglist($mobile)
{
    $imageDir = '../userdata/content/images/';
    $ratingCounts = [];
    foreach (glob($imageDir . '*.yml') as $filePath) {
        $data = Yaml::parseFile($filePath);
        $rating = (int)($data['rating'] ?? 0);
        $ratingCounts[$rating] = ($ratingCounts[$rating] ?? 0) + 1;
    }
    for ($i = 0; $i <= 5; $i++) {
        $ratingCounts[$i] = $ratingCounts[$i] ?? 0;
    }
    krsort($ratingCounts);
    foreach ($ratingCounts as $rating => $count) {
        $stars = '';
        for ($i = 1; $i <= 5; $i++) {
            $colorClass = $i <= $rating ? 'text-sky-400' : 'text-gray-300';
            $stars .= "<svg class='w-4 h-4 inline-block $colorClass' viewBox='0 0 20 20' fill='currentColor'><path d='M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.137 3.5h3.684c.969 0 1.371 1.24.588 1.81l-2.984 2.17 1.138 3.5c.3.921-.755 1.688-1.538 1.117L10 13.348l-2.976 2.176c-.783.571-1.838-.196-1.538-1.117l1.138-3.5-2.984-2.17c-.783-.57-.38-1.81.588-1.81h3.684l1.137-3.5z'/></svg>";
        }
        $html = $mobile
            ? "<div class=\"pl-5\"><a href=\"media.php?rating=$rating\" class=\"block px-4 text-base font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-800 sm:px-6\">$stars ($count)</a></div>"
            : "<li><a href=\"media.php?rating=$rating\" class=\"text-gray-400 hover:text-sky-400\">$stars ($count)</a></li>";
        echo $html . "\n";
    }
}

function getImagesFromDirectory($directory = "../userdata/content/images/") {
    return is_dir($directory)
        ? glob($directory . '*.{jpg,jpeg,png,gif}', GLOB_BRACE)
        : [];
}

function getAllUploadedImages()
{
    $imageDir = '../userdata/content/images/';
    $images = getImagesFromDirectory($imageDir);
    $imageData = [];
    foreach ($images as $image) {
        $fileName = basename($image);
        $yamlFile = $imageDir . pathinfo($fileName, PATHINFO_FILENAME) . '.yml';
        if (!file_exists($yamlFile)) continue;
        $metadata = Yaml::parseFile($yamlFile);
        $title = htmlspecialchars($metadata['title'] ?? 'Kein Titel');
        $description = htmlspecialchars($metadata['description'] ?? 'Keine Beschreibung verfügbar');
        $exifDate = $metadata['exif']['Date'] ?? null;
        $year = $exifDate ? substr($exifDate, 0, 4) : null;
        $rating = $metadata['rating'] ?? 0;
        $imageData[] = [
            'filename' => $fileName,
            'title' => $title,
            'description' => $description,
            'year' => $year,
            'rating' => $rating,
        ];
    }
    return $imageData;
}
