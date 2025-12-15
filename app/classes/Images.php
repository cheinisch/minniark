<?php

use Symfony\Component\Yaml\Yaml;

class Images
{
  private string $imageDir;
  private string $cacheDirWeb;

  public function __construct()
  {
    $this->imageDir = realpath(__DIR__ . '/../userdata/content/images') . DIRECTORY_SEPARATOR;
    // Web-Pfad (wichtig: in admin meist ../cache/images/)
    $this->cacheDirWeb = '../cache/images/';
  }

  /**
   * @param array $albumSlugs z.B. ['urlaub-2025', 'winter']
   * @param string $size 'S'|'M'|'L'
   * @return array [{ filename,title,thumb,album }]
   */
  public function getImagesFromAlbums(array $albumSlugs, string $size = 'S'): array
  {
    $out = [];

    foreach ($albumSlugs as $albumSlug) {
      $album = getAlbumData($albumSlug);
      $imgs = $album['images'] ?? [];

      foreach ($imgs as $imgName) {
        $meta = $this->readMetaByFilename($imgName);
        if (!$meta) continue;

        $guid = $meta['guid'] ?? null;
        if (!$guid) continue;

        $title = $meta['title'] ?? $imgName;

        $out[] = [
          'filename' => $imgName,
          'title'    => $title,
          'album'    => $albumSlug,
          'thumb'    => $this->cacheDirWeb . $guid . '_' . strtoupper($size) . '.jpg',
        ];
      }
    }

    return $out;
  }

  private function readMetaByFilename(string $imgName): ?array
  {
    $base = pathinfo($imgName, PATHINFO_FILENAME);
    $ymlPath = $this->imageDir . $base . '.yml';

    if (!file_exists($ymlPath)) return null;

    try {
      $yaml = Yaml::parseFile($ymlPath);
      return $yaml['image'] ?? null;
    } catch (\Throwable $e) {
      return null;
    }
  }
}
