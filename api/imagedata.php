<?php

    // Content-Type setzen, damit der Client weiß, dass es sich um JSON handelt
header('Content-Type: application/json');

$arr = array ( 
      "imagedata"=>array( 
          "imagepath"=>"https://picsum.photos/1920/1080", 
          "datapath" => "",
          "title" => "Dummytitle",
          "description" => "Content"

      )
  ); 
  
  // Function to convert array into JSON 
  echo json_encode($arr); 

?>