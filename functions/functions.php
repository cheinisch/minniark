<?php

// Dashboard-Funktionen einbinden
require_once __DIR__ . '/func_dashboard.php';

require_once __DIR__ . '/func_essay.php';

require_once __DIR__ . '/func_page.php';





/**
 * Generiert einen URL-freundlichen Slug aus einem Titel.
 *
 * @param string $title Titel des Pages.
 * @return string URL-freundlicher Slug.
 */
function generateSlug($title) {
    $slug = strtolower(trim($title));
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
    $slug = trim($slug, '-');
    return $slug;
}

function func_test()
{
    echo "TEST";
}