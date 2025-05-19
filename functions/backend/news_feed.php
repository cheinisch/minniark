<?php


    function getNewsFeed(): array
    {
        $url = "https://minniark.app/blog.rss";
        $feed = [];

        // Feed laden
        $rss = @simplexml_load_file($url);

        if ($rss === false) {
            return [];
        }

        foreach ($rss->channel->item as $item) {
            // CDATA und HTML entfernen, Text kürzen
            $rawDescription = (string) $item->description;
            $plainText = strip_tags($rawDescription);
            $shortDescription = mb_strimwidth($plainText, 0, 300, '…');
             $date = new DateTime((string) $item->pubDate);
            $formattedDate = $date->format('d. M Y'); // z.B. 14. Mai 2025

            $feed[] = [
                'title' => (string) $item->title,
                'link' => (string) $item->link,
                'pubDate' => (string) $formattedDate,
                'description' => $shortDescription,
                'categories' => array_map('strval', iterator_to_array($item->category))
            ];
        }

        return $feed;
    }
