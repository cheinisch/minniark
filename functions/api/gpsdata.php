<?php

    // Lese GPS-Daten aus EXIF
function getGPSData($exif) {
    if (!isset($exif['GPSLatitude'], $exif['GPSLongitude'], $exif['GPSLatitudeRef'], $exif['GPSLongitudeRef'])) {
        return "Not available";
    }

    $lat = convertGPSToDecimal($exif['GPSLatitude'], $exif['GPSLatitudeRef']);
    $lon = convertGPSToDecimal($exif['GPSLongitude'], $exif['GPSLongitudeRef']);
    
    return ["latitude" => $lat, "longitude" => $lon];
}

// Umwandlung von GPS-Koordinaten ins Dezimalformat
function convertGPSToDecimal($coord, $ref) {
    $degrees = count($coord) > 0 ? gps2Num($coord[0]) : 0;
    $minutes = count($coord) > 1 ? gps2Num($coord[1]) : 0;
    $seconds = count($coord) > 2 ? gps2Num($coord[2]) : 0;

    $decimal = $degrees + ($minutes / 60) + ($seconds / 3600);
    return ($ref === 'S' || $ref === 'W') ? -$decimal : $decimal;
}

// Hilfsfunktion zur Umwandlung von GPS-Koordinaten
function gps2Num($coordPart) {
    $parts = explode('/', $coordPart);
    return (count($parts) > 1) ? floatval($parts[0]) / floatval($parts[1]) : floatval($parts[0]);
}