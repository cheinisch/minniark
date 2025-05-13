<?php

    function isInList($slug, $albumList) {
        foreach ($albumList as $album) {
            if (isset($album['Slug']) && $album['Slug'] === $slug) {
                return $album['Name'];
            }
        }
        return "---"; // oder return ''; wenn du lieber einen leeren String möchtest
    }

    function isInListPage($slug, $pageList) {
        foreach ($pageList as $page) {
            if (isset($page['slug']) && $page['slug'] === $slug) {
                return $page['title'];
            }
        }
        return "---"; // oder return ''; wenn du lieber einen leeren String möchtest
    }