<?php


    function extractExifData($filePath) {
        error_log("Start Exif function");
        error_log("File Path: ".$filePath);
        $exif = @exif_read_data($filePath, 0, true);
        if (!$exif || !is_array($exif)) return [];

        return [
            "Camera" => !empty($exif['IFD0']['Make']) && !empty($exif['IFD0']['Model']) 
                ? trim($exif['IFD0']['Make'] . ' ' . $exif['IFD0']['Model']) 
                : "Unknown",

            "Lens" => $exif['EXIF']['UndefinedTag:0xA434'] ?? "Unknown",

            "Aperture" => !empty($exif['EXIF']['FNumber']) 
                ? "f/" . round(rationalToFloat($exif['EXIF']['FNumber']), 1) 
                : "Unknown",

            "Shutter Speed" => $exif['EXIF']['ExposureTime'] ?? "Unknown",

            "Focal Length" => !empty($exif['EXIF']['FocalLength']) 
                ? formatFocalLength($exif['EXIF']['FocalLength']) 
                : "Unknown",

            "ISO" => $exif['EXIF']['ISOSpeedRatings'] ?? "Unknown",

            "Date" => $exif['EXIF']['DateTimeOriginal'] 
                ?? $exif['IFD0']['DateTime'] 
                ?? "Unknown",

            "Flash" => $exif['EXIF']['Flash'] ?? "Unknown",

            "White Balance" => $exif['EXIF']['WhiteBalance'] ?? "Unknown",

            "Metering Mode" => $exif['EXIF']['MeteringMode'] ?? "Unknown",

            "Exposure Program" => $exif['EXIF']['ExposureProgram'] ?? "Unknown",

            "Exposure Compensation" => $exif['EXIF']['ExposureBiasValue'] ?? "Unknown",

            "Max Aperture" => isset($exif['EXIF']['MaxApertureValue']) 
                ? "f/" . round(rationalToFloat($exif['EXIF']['MaxApertureValue']), 1) 
                : "Unknown",

            "Digital Zoom" => isset($exif['EXIF']['DigitalZoomRatio']) 
                ? rationalToFloat($exif['EXIF']['DigitalZoomRatio']) . "x"
                : "Unknown",

            "Orientation" => $exif['IFD0']['Orientation'] ?? "Unknown",

            "Software" => $exif['IFD0']['Software'] ?? "Unknown",

            "Color Space" => $exif['EXIF']['ColorSpace'] ?? "Unknown",

            "Light Source" => $exif['EXIF']['LightSource'] ?? "Unknown",

            "GPS" => getGPSData($exif['GPS'] ?? [])
        ];
    }

    function rationalToFloat($value) {
        if (strpos($value, '/') !== false) {
            [$num, $den] = explode('/', $value);
            if ((float)$den != 0) {
                return (float)$num / (float)$den;
            }
        }
        return (float)$value;
    }
    
    function formatFocalLength($value) {
        $float = rationalToFloat($value);
        return round($float) . "mm";
    }
    
