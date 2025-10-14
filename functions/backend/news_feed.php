<?php
function getNewsFeed(int $limit = 3): array
{
    $url = "https://minniark.app/blog.rss";

    // Feed laden
    $rss = @simplexml_load_file($url);
    if ($rss === false) {
        return [];
    }

    // Items sammeln inkl. Unix-Timestamp für sauberes Sortieren
    $items = [];
    foreach ($rss->channel->item as $item) {
        $rawDescription = (string) $item->description;
        $plainText = strip_tags($rawDescription);
        $shortDescription = mb_strimwidth($plainText, 0, 300, '…');

        $pubRaw = (string) $item->pubDate;
        $ts = strtotime($pubRaw) ?: 0;

        // Datum formatiert (lokale TZ)
        $date = (new DateTimeImmutable("@$ts"))
            ->setTimezone(new DateTimeZone(date_default_timezone_get()));
        $formattedDate = $date->format('d. M Y');

        $items[] = [
            'title' => (string) $item->title,
            'link' => (string) $item->link,
            'pubDate' => $formattedDate,
            'description' => $shortDescription,
            'categories' => array_map('strval', iterator_to_array($item->category)),
            '_ts' => $ts, // Hilfsfeld für Sortierung
        ];
    }

    // Nach Datum absteigend sortieren
    usort($items, fn($a, $b) => $b['_ts'] <=> $a['_ts']);

    // Auf die neuesten N begrenzen
    $items = array_slice($items, 0, $limit);

    // Hilfsfeld entfernen
    foreach ($items as &$i) {
        unset($i['_ts']);
    }

    return $items;
}
