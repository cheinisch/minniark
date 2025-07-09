<?php


    function extractExifData($filePath) {
        error_log("Start Exif function");
        error_log("File Path: ".$filePath);
        $exif = @exif_read_data($filePath, 0, true);
        if (!$exif || !is_array($exif)) return [];

        return [
            "Camera" => !empty($exif['IFD0']['Make']) && !empty($exif['IFD0']['Model']) 
                ? trim($exif['IFD0']['Make'] . ' ' . $exif['IFD0']['Model']) 
                : "",

            "Lens" => $exif['EXIF']['UndefinedTag:0xA434'] ?? "",

            "Aperture" => !empty($exif['EXIF']['FNumber']) 
                ? "f/" . round(rationalToFloat($exif['EXIF']['FNumber']), 1) 
                : "",

            "Shutter Speed" => $exif['EXIF']['ExposureTime'] ?? "",

            "Focal Length" => !empty($exif['EXIF']['FocalLength']) 
                ? formatFocalLength($exif['EXIF']['FocalLength']) 
                : "",

            "ISO" => $exif['EXIF']['ISOSpeedRatings'] ?? "",

            "Date" => $exif['EXIF']['DateTimeOriginal'] 
                ?? $exif['IFD0']['DateTime'] 
                ?? "",

            "Flash" => $exif['EXIF']['Flash'] ?? "",

            "White Balance" => $exif['EXIF']['WhiteBalance'] ?? "",

            "Metering Mode" => $exif['EXIF']['MeteringMode'] ?? "",

            "Exposure Program" => $exif['EXIF']['ExposureProgram'] ?? "",

            "Exposure Compensation" => $exif['EXIF']['ExposureBiasValue'] ?? "",

            "Max Aperture" => isset($exif['EXIF']['MaxApertureValue']) 
                ? "f/" . round(rationalToFloat($exif['EXIF']['MaxApertureValue']), 1) 
                : "",

            "Digital Zoom" => isset($exif['EXIF']['DigitalZoomRatio']) 
                ? rationalToFloat($exif['EXIF']['DigitalZoomRatio']) . "x"
                : "",

            "Orientation" => $exif['IFD0']['Orientation'] ?? "",

            "Software" => $exif['IFD0']['Software'] ?? "",

            "Color Space" => $exif['EXIF']['ColorSpace'] ?? "",

            "Light Source" => $exif['EXIF']['LightSource'] ?? "",

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
    
