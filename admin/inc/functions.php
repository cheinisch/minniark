<?php

require 'db-connect.php';
require 'image-cache.php';
require 'exif.php';


function trunc($phrase, $max_words) {
    $phrase_array = explode(' ',$phrase);
    if(count($phrase_array) > $max_words && $max_words > 0)
       $phrase = implode(' ',array_slice($phrase_array, 0, $max_words)).'...';
    return $phrase;
 }

 function login($user, $password)
 {

    // Daten aus der Datei einbinden
    $userDataString = include './../config/userdata.php';

    $userDataString = 'a:1:{i:0;O:*:"~":3:{s:4:"name";s:9:"heimfisch";s:5:"email";s:26:"info@christian-heinisch.de";s:12:"passwordHash";s:4:"hallo";}}';

    // Überprüfen, ob der String gültige serialisierte Daten enthält
if (is_serialized($userDataString)) {
    // Daten deserialisieren
    $userData = unserialize($userDataString);

    // Daten auslesen
    $name = $userData[0]->name;
    $email = $userData[0]->email;
    $passwordHash = $userData[0]->passwordHash;

    // Ausgabe der Daten (nur zu Demonstrationszwecken)
    echo "Name: $name<br>";
    echo "Email: $email<br>";
    echo "Password Hash: $passwordHash<br>";
} else {
    // Wenn der String nicht gültig serialisiert ist, gib eine Fehlermeldung aus oder führe eine alternative Logik aus
    echo "Die Daten sind nicht gültig serialisiert.";
}

    // Daten deserialisieren
    $userData = unserialize(base64_decode($userDataString));

    // Daten auslesen
    $name = $userData[0]->name;
    $email = $userData[0]->email;
    $passwordHash = $userData[0]->passwordHash;

    if(strcasecmp($user, $name) == 0 && strcmp($passwordHash, $password) == 0)
    {
        echo 'passt';
        #return true;
    }else{
        echo 'passt nicht';
        #return false;
    }

    

 }

 function is_serialized($data) {
    // Wenn der String nicht mit 'a:' beginnt, ist er nicht serialisiert
    if ('a:' !== substr($data, 0, 2)) {
        return false;
    }
    
    // Versuche, den String zu deserialisieren
    $unserialized = @unserialize($data);
    
    // Wenn unserialize() erfolgreich ist, dann ist der String serialisiert
    return $unserialized !== false;
}


/*
Public Exif functions
*/

function ip_get_exif_date()
{

    $image = ip_get_image("original");
    $ext = pathinfo($image, PATHINFO_EXTENSION);
    if($ext == "jpg")
    {
    $sitedata = get_sitedata();

    $data = cameraUsed(ip_get_image("original"));

    $date = new DateTime($data["date"]);
    echo $date->format($sitedata["site-date"]);
    }else{
        echo "No Exifdata found";
    }
}

function ip_get_exif_time()
{
    $image = ip_get_image("original");
    $ext = pathinfo($image, PATHINFO_EXTENSION);
    if($ext == "jpg")
    {
        $sitedata = get_sitedata();

        $data = cameraUsed(ip_get_image("original"));

        $time = new DateTime($data["date"]);
        echo $time->format($sitedata["site-hours"]);
    }else{
        echo "No Exifdata found";
    }
}

function ip_get_exif_iso()
{
    $image = ip_get_image("original");
    $ext = pathinfo($image, PATHINFO_EXTENSION);
    if($ext == "jpg")
    {
    $data = cameraUsed(ip_get_image("original"));

    echo $data["iso"];
    }else{
        echo "No Exifdata found";
    }
}

function ip_get_exif_aperture()
{
    $image = ip_get_image("original");
    $ext = pathinfo($image, PATHINFO_EXTENSION);
    if($ext == "jpg")
    {
    $data = cameraUsed(ip_get_image("original"));

    echo $data["aperture"];
}else{
    echo "No Exifdata found";
}
}

function ip_get_exif_exposure_time()
{
    $image = ip_get_image("original");
    $ext = pathinfo($image, PATHINFO_EXTENSION);
    if($ext == "jpg")
    {
    $data = cameraUsed($image);
    

    echo $data["exposure"];
    }else{
        echo "No Exifdata found";
    }
}

function ip_get_exif_camera_make()
{
    $image = ip_get_image("original");
    $ext = pathinfo($image, PATHINFO_EXTENSION);
    if($ext == "jpg")
    {
    $data = cameraUsed(ip_get_image("original"));

    echo $data["make"];
}else{
    echo "No Exifdata found";
}
}

function ip_get_exif_camera_model()
{
    $image = ip_get_image("original");
    $ext = pathinfo($image, PATHINFO_EXTENSION);
    if($ext == "jpg")
    {
    $data = cameraUsed(ip_get_image("original"));

    echo $data["model"];
}else{
    echo "No Exifdata found";
}
}

?>