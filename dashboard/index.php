<?php
// Die komplette URI ermitteln
$requestUri = $_SERVER['REQUEST_URI'];

// Den Pfadanteil aus der URL extrahieren (ohne Query-Parameter)
$path = parse_url($requestUri, PHP_URL_PATH);

// Den Pfad in einzelne Segmente aufteilen
$segments = explode('/', trim($path, '/'));
print_r($segments);
// Beispielhafte Routenverarbeitung:
if (isset($segments[2])) {
    switch ($segments[2]) {
        case 'essay':
            // URL: /user/42
            $userId = $segments[1] ?? null;
            echo "Benutzerseite für ID: " . htmlspecialchars($userId);
            break;
        case 'page':
            // URL: /produkt/123
            $produktId = $segments[1] ?? null;
            echo "Produktseite für ID: " . htmlspecialchars($produktId);
            break;
        case 'media':
            include('media.php');
            break;
        default:
            echo "Startseite oder unbekannte Route.";
            break;
    }
} else {
    echo "Startseite";
}
?>