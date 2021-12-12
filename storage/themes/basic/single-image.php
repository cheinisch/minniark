<?php
include $theme."header.php";
?>
<div class="row">
    <img src="<?php echo ip_get_image("original"); ?>">
</div>
<div class="row">
    <h3><?php echo ip_get_image_title(); ?></h3>
    <i><?php ip_get_exif_date(); ?> - <?php ip_get_exif_time(); ?> - ISO: <?php ip_get_exif_iso(); ?> - Exposure Time: <?php ip_get_exif_exposure_time(); ?> - Aperture: <?php ip_get_exif_aperture(); ?></i>
    <i>Camera <?php ip_get_exif_camera_make(); ?> Model <?php ip_get_exif_camera_model(); ?></i>
    <p><?php echo ip_get_image_text(); ?></p>
</div>

<?php
    include $theme."footer.php";
    ?>