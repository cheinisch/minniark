<?php
    /**
     * @static
     * @param $source string Path for source image
     * @param $destination string Path for destination image to be placed
     * @param $targetWidth int Width of the new image (in pixels)
     * @param $targetHeight int Height of the new image (in pixels)
     * @param $strict bool
     */
    function createImage($source, $destination, $targetSize, $strict = false){

        ini_set('memory_limit', '1024M');

        list($width, $height) = getimagesize($source);

        echo "breite ".$width;

        if($width > $height)
        {
            $ratio = $width / $targetSize;
            $targetWidth = $targetSize;
            $targetHeight = $height / $ratio;

        }else{
            
                $ratio = $height / $targetSize;
                $targetHeight = $targetSize;
                $targetWidth = $width / $ratio;
    
            }
        

        $dir = dirname($destination);
        if(!is_dir($dir)){
            mkdir($dir, 0770, true);
        }
        $fileContents = file_get_contents($source);
        $image = imagecreatefromstring($fileContents);

        $thumbnail = resizeImage($image, $targetWidth, $targetHeight, $strict);
        imagejpeg($thumbnail, $destination, 100);
        imagedestroy($thumbnail);
        imagedestroy($image);
    }

    /**
     * Resize an image to the specified dimensions
     * @param string $original Path to the original image
     * @param int $targetWidth Width of the new image (in pixels)
     * @param int $targetHeight Height of the new image (in pixels)
     * @param bool $strict True to crop the picture to the specified dimensions, false for best fit
     * @return bool|resource Returns the new image resource or false if the image was not resized.
     */
    function resizeImage($original, $targetWidth, $targetHeight, $strict = false)
    {
        $originalWidth = imagesx($original);
        $originalHeight = imagesy($original);

        $widthRatio = $targetWidth / $originalWidth;
        $heightRatio = $targetHeight / $originalHeight;

        if(($widthRatio > 1 || $heightRatio > 1) && !$strict){
            // don't scale up an image if either targets are greater than the original sizes and we aren't using a strict parameter
            $dstHeight = $originalHeight;
            $dstWidth = $originalWidth;
            $srcHeight = $originalHeight;
            $srcWidth = $originalWidth;
            $srcX = 0;
            $srcY = 0;
        }elseif ($widthRatio > $heightRatio) {
            // width is the constraining factor
            if ($strict) {
                $dstHeight = $targetHeight;
                $dstWidth = $targetWidth;
                $srcHeight = $originalHeight;
                $srcWidth = $heightRatio * $targetWidth;
                $srcX = floor(($originalWidth - $srcWidth) / 2);
                $srcY = 0;
            } else {
                $dstHeight = ($originalHeight * $targetWidth) / $originalWidth;
                $dstWidth = $targetWidth;
                $srcHeight = $originalHeight;
                $srcWidth = $originalWidth;
                $srcX = 0;
                $srcY = 0;
            }
        } else {
            // height is the constraining factor
            if ($strict) {
                $dstHeight = $targetHeight;
                $dstWidth = $targetWidth;
                $srcHeight = $widthRatio * $targetHeight;
                $srcWidth = $originalWidth;
                $srcY = floor(($originalHeight - $srcHeight) / 2);
                $srcX = 0;
            } else {
                $dstHeight = $targetHeight;
                $dstWidth = ($originalWidth * $targetHeight) / $originalHeight;
                $srcHeight = $originalHeight;
                $srcWidth = $originalWidth;
                $srcX = 0;
                $srcY = 0;
            }
        }

        $new = imagecreatetruecolor($dstWidth, $dstHeight);
        if ($new === false) {
            return false;
        }

        imagecopyresampled($new, $original, 0, 0, $srcX, $srcY, $dstWidth, $dstHeight, $srcWidth, $srcHeight);

        return $new;
    }



function resize_image($path, $newpath,$filename, $w, $h, $crop=FALSE) {
    $temp = $path.''. $filename;
    $file = realpath($temp);
    echo $file;
    
    $original = imagecreatefromjpeg($file);
    $resized = imagecreatetruecolor(400, 300);
    imagecopyresampled($resized, $original, 0, 0, 0, 0, 400, 300,null,null);
    imagejpeg($resized, $newpath."thumb_".$filename); 
}

function generateThumbnail($img, $width, $height, $quality = 90)
{
    if (is_file($img)) {
        $imagick = new Imagick(realpath($img));
        $imagick->setImageFormat('jpeg');
        $imagick->setImageCompression(Imagick::COMPRESSION_JPEG);
        $imagick->setImageCompressionQuality($quality);
        $imagick->thumbnailImage($width, $height, false, false);
        $filename_no_ext = reset(explode('.', $img));
        if (file_put_contents('..\\cache\\thumb_'.$filename_no_ext.'.jpg', $imagick) === false) {
            throw new Exception("Could not put contents.");
        }
        return true;
    }
    else {
        throw new Exception("No valid image provided with {$img}.");
    }
}

function generateMedium($img, $width, $height, $quality = 90)
{
    if (is_file($img)) {
        $imagick = new Imagick(realpath($img));
        $imagick->setImageFormat('jpeg');
        $imagick->setImageCompression(Imagick::COMPRESSION_JPEG);
        $imagick->setImageCompressionQuality($quality);
        $imagick->thumbnailImage($width, $height, false, false);
        $filename_no_ext = reset(explode('.', $img));
        if (file_put_contents('..\\cache\\medium_'.$filename_no_ext. '.jpg', $imagick) === false) {
            throw new Exception("Could not put contents.");
        }
        return true;
    }
    else {
        throw new Exception("No valid image provided with {$img}.");
    }
}

function generateLarge($img, $width, $height, $quality = 90)
{
    if (is_file($img)) {
        $imagick = new Imagick(realpath($img));
        $imagick->setImageFormat('jpeg');
        $imagick->setImageCompression(Imagick::COMPRESSION_JPEG);
        $imagick->setImageCompressionQuality($quality);
        $imagick->thumbnailImage($width, $height, false, false);
        $filename_no_ext = reset(explode('.', $img));
        if (file_put_contents('large_'.$filename_no_ext .'.jpg', $imagick) === false) {
            throw new Exception("Could not put contents.");
        }
        return true;
    }
    else {
        throw new Exception("No valid image provided with {$img}.");
    }
}

// example usage
/*try {
    generateThumbnail('test.jpg', 100, 50, 65);
}
catch (ImagickException $e) {
    echo $e->getMessage();
}

*/
?>