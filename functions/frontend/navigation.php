<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    function buildNavigation(string $templateDir): array
    {
        $nav = [];

        $timelineEnable = is_timeline_enabled();
        $mapEnable = is_map_enabled();

        // Prüfe, ob map.twig existiert
        $hasMap = file_exists($templateDir . '/map.twig');

        // Prüfe, ob timeline.twig existiert
        $hasTimeline = file_exists($templateDir . '/timeline.twig');

        // Prüfe, ob timeline.twig existiert
        $hasBlog = file_exists($templateDir . '/blog.twig');
        $hasBlogItems = hasBlogPosts();

        // Home ist immer dabei
        $nav[] = ['title' => 'Home', 'url' => '/home'];

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

        return $nav;
    }


