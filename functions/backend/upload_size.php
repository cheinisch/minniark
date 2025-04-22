<?php

    function get_uploadsize()
    {

        $upload_size = convertToReadableSize(ini_get('upload_max_filesize'));

        return $upload_size;
    }

    function convertToReadableSize($size) {
        $unit = strtoupper(substr($size, -1));
        $value = (int) $size;
    
        switch ($unit) {
            case 'G':
                $bytes = $value * 1024 * 1024 * 1024;
                break;
            case 'M':
                $bytes = $value * 1024 * 1024;
                break;
            case 'K':
                $bytes = $value * 1024;
                break;
            default:
                $bytes = (int) $size;
        }
    
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' B';
        }
    }
    