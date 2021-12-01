<?php
include $theme."header.php";
?>
<div class="row row-cols-1 row-cols-sm-2 row-cols-md-12 g-3">
    <h2><?php echo ip_get_album_title(); ?></h2>
</div>
<div class="row row-cols-12 row-cols-sm-12 row-cols-md-12 g-3">
    <?php echo ip_get_album_description(); ?>
</div>
<div class="row" data-masonry='{"percentPosition": true }'>
    <?php echo ip_get_album_images('<div class="col-sm-6 col-lg-4 mb-4">
      <div class="card">
        <a href="{{image-id}}"><img class="card-img-top" width="100%" height="100%" src="{{image.medium}}"></a>
      </div>
    </div>'); ?>
</div>

<?php
    include $theme."footer.php";
    ?>
