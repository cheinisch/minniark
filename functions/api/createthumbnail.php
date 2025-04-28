<?php 

// Erstelle ein Thumbnail in der gewünschten Größe
function createThumbnail($source, $destination, $newWidth) {
    error_log("Start createThumbnail for source: $source");

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
            error_log("Image type: JPEG");
            $image = @imagecreatefromjpeg($source);
            break;
        case IMAGETYPE_PNG:
            error_log("Image type: PNG");
            $image = @imagecreatefrompng($source);
            break;
        case IMAGETYPE_GIF:
            error_log("Image type: GIF");
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
            if (!imagejpeg($thumb, $destination, 85)) {
                error_log("Failed to save JPEG thumbnail to: $destination");
            }
            break;
        case IMAGETYPE_PNG:
            if (!imagepng($thumb, $destination, 8)) {
                error_log("Failed to save PNG thumbnail to: $destination");
            }
            break;
        case IMAGETYPE_GIF:
            if (!imagegif($thumb, $destination)) {
                error_log("Failed to save GIF thumbnail to: $destination");
            }
            break;
    }

    error_log("Thumbnail successfully created at: $destination");

    imagedestroy($thumb);
    imagedestroy($image);

    return true;
}
