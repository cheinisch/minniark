<?php
/*
Get All Exifdata from Picture
*/


function cameraUsed($imagePath) {

    // Check if the variable is set and if the file itself exists before continuing
    if ((isset($imagePath)) and (file_exists($imagePath))) {
   
      // There are 2 arrays which contains the information we are after, so it's easier to state them both
      $exif_ifd0 = exif_read_data($imagePath ,'IFD0' ,0);      
      $exif_exif = exif_read_data($imagePath ,'EXIF' ,0);
     
      //error control
      $notFound = "Unavailable";
     
      // Make
      if (@array_key_exists('Make', $exif_ifd0)) {
        $camMake = $exif_ifd0['Make'];
      } else { $camMake = $notFound; }
     
      // Model
      if (@array_key_exists('Model', $exif_ifd0)) {
        $camModel = $exif_ifd0['Model'];
      } else { $camModel = $notFound; }
     
      // Exposure
      if (@array_key_exists('ExposureTime', $exif_ifd0)) {
        $camExposure = $exif_ifd0['ExposureTime'];
      } else { $camExposure = $notFound; }

      // Aperture
      if (@array_key_exists('ApertureFNumber', $exif_ifd0['COMPUTED'])) {
        $camAperture = $exif_ifd0['COMPUTED']['ApertureFNumber'];
      } else { $camAperture = $notFound; }
     
      // Date
      if (@array_key_exists('DateTime', $exif_ifd0)) {
        $camDate = $exif_ifd0['DateTime'];
      } else { $camDate = $notFound; }
     
      // ISO
      if (@array_key_exists('ISOSpeedRatings',$exif_exif)) {
        $camIso = $exif_exif['ISOSpeedRatings'];
      } else { $camIso = $notFound; }
     
      $return = array();
      $return['make'] = $camMake;
      $return['model'] = $camModel;
      $return['exposure'] = $camExposure;
      $return['aperture'] = $camAperture;
      $return['date'] = $camDate;
      $return['iso'] = $camIso;
      return $return;
   
    } else {
      return false;
    }
}

?>