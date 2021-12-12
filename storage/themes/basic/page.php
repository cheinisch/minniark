<?php
include $theme."header.php";
?>
<article class="blog-post">
    <h2 class="blog-post-title"><?php echo ip_get_page_title(); ?></h2>
    <?php echo ip_get_page_text(); ?>
    </article>
    <?php
    include $theme."footer.php";
    ?>