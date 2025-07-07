<?php 

function createThumbnail($source, $destination, $newWidth) {
    error_log("Start createThumbnail for source: $source");

    // Erhöhe Memory Limit, falls notwendig
    ini_set('memory_limit', '512M');

    // Nutze Imagick, wenn verfügbar
    if (extension_loaded('imagick')) {
        error_log("Using Imagick for thumbnail creation");

        try {
            $imagick = new Imagick($source);

            // Optional: nur Bilder verarbeiten
            if (!$imagick->getImageFormat()) {
                error_log("Invalid image format for: $source");
                return false;
            }

            $imagick->setImageOrientation(imagick::ORIENTATION_TOPLEFT); // Korrigiert ggf. EXIF-Rotation
            $imagick->thumbnailImage($newWidth, 0); // Höhe wird proportional berechnet
            $imagick->setImageCompressionQuality(85);
            $imagick->writeImage($destination);

            $imagick->clear();
            $imagick->destroy();

            error_log("Thumbnail successfully created at: $destination using Imagick");
            return true;
        } catch (Exception $e) {
            error_log("Imagick failed: " . $e->getMessage());
            // Fallback auf GD
        }
    }

    // --- GD Fallback ---
    error_log("Falling back to GD for thumbnail creation");

    $imageInfo = @getimagesize($source);
    if (!$imageInfo) {
        error_log("Failed to read image size for: $source");
        return false;
    }

    list($width, $height, $type) = $imageInfo;

    if (empty($width) || empty($height)) {
        error_log("Invalid image dimensions: width=$width, height=$height for source: $source");
        return false;
    }

    $newHeight = intval(($height / $width) * $newWidth);

    switch ($type) {
        case IMAGETYPE_JPEG:
            $image = @imagecreatefromjpeg($source);
            break;
        case IMAGETYPE_PNG:
            $image = @imagecreatefrompng($source);
            break;
        case IMAGETYPE_GIF:
            $image = @imagecreatefromgif($source);
            break;
        default:
            error_log("Unsupported image type: $type");
            return false;
    }

    if (!$image) {
        error_log("Failed to create image resource from source: $source");
        return false;
    }

    $thumb = imagecreatetruecolor($newWidth, $newHeight);
    if (!$thumb) {
        error_log("Failed to create thumbnail resource");
        return false;
    }

    if (!imagecopyresampled($thumb, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height)) {
        error_log("Failed to resample image for thumbnail");
        imagedestroy($image);
        imagedestroy($thumb);
        return false;
    }

    switch ($type) {
        case IMAGETYPE_JPEG:
            imagejpeg($thumb, $destination, 85);
            break;
        case IMAGETYPE_PNG:
            imagepng($thumb, $destination, 8);
            break;
        case IMAGETYPE_GIF:
            imagegif($thumb, $destination);
            break;
    }

    imagedestroy($thumb);
    imagedestroy($image);

    error_log("Thumbnail successfully created at: $destination using GD");
    return true;
}
