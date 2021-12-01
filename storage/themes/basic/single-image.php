<div class="row">
    <img src="<?php echo ip_get_image("original"); ?>">
</div>
<div class="row">
    <h3><?php echo ip_get_image_title(); ?></h3>
    <i><?php ip_get_exif_date(); ?> - ISO: <?php ip_get_exif_iso(); ?> - Belichtung: <?php ip_get_exif_exposure_time(); ?> - Blende: <?php ip_get_exif_aperture(); ?></i>
    <p><?php echo ip_get_image_text(); ?></p>
</div>