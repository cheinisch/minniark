<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="Christian Heinisch">
        <meta name="generator" content="Hugo 0.88.1">
        <title><?php echo ip_get_sitetitle(); ?></title>
        <meta name="keywords" content="<?php echo ip_get_sitekeywords(); ?>"/>

        <!-- CSS -->
        <link href="<?php echo pcs_get_theme_path(); ?>css/bootstrap.min.css" rel="stylesheet">

        <!-- JS -->
    </head>
    <body>
        <div class="container">
            <div class="text-center">
                <h1 class="display-5"><?php echo ip_get_sitename(); ?></h1>
                <p><?php echo ip_get_sitetagline(); ?></p>
                <header class="d-flex justify-content-center py-3">
      <ul class="nav nav-pills">
        <?php pcs_get_main_menu('<li class="nav-item"><a href="index.php?content={{type}}" class="nav-link">{{title}}</a></li>','<li class="nav-item"><a href="index.php?content={{type}}" class="nav-link active">{{title}}</a></li>'); ?>
      </ul>
    </header>
            </div>
