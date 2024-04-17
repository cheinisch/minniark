<?php

/*
 * Check if the accountfile exist
 */

function userfile_exist()
{

    if(file_exists('conf/userdata.php'))
    {
        return true;
    }else{
        return false;
    }

    return false;
}

/*
 * Create Account File
 */

 function downloadJSON($data, $filename) {
    // JSON-Daten in eine Zeichenkette konvertieren
    $jsondata = json_encode($data, JSON_PRETTY_PRINT);

    $json = '<?php
defined('IMAGEPORTFOLIO') or die();    
return \''.$jsondata.'\';
?>';


    // HTTP-Header für den Download festlegen
    header('Content-Description: File Transfer');
    header('Content-Type: application/json');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . strlen($json));

    // JSON-Daten ausgeben
    echo $json;

    // Skript beenden, um unerwünschte zusätzliche Ausgaben zu verhindern
    exit;
}


?>