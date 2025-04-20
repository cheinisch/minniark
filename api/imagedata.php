<?php

require_once( __DIR__ . "/../functions/function_api.php");
secure_API();

    // Content-Type setzen, damit der Client weiß, dass es sich um JSON handelt
header('Content-Type: application/json');

$arr = array ( 
    "imagepath"=>"https://picsum.photos/1920/1080", 
    "datapath" => "",
    "title" => "Dummytitle",
    "description" => "Content",
    "exif"=>array(
        "Camera" => "Canon 250D",
        "Lens"=> "Unknown",
        "Aperture"=> "Unknown",
        "Shutter Speed"=> "60\/1",
        "ISO"=> 3200,
        "Date"=> "Unknown",
        "GPS"=>array(
            "latitude"=> 50.497521748333334,
            "longitude"=> 9.933594423333334
        )
      )
  ); 
  
  // Function to convert array into JSON 
  echo json_encode($arr); 

?>