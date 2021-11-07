<?php

  include 'inc/functions.php';

  $ini = parse_ini_file('app.ini');

  $pagetype = 'default';

	if (isset($_GET['page']))
	{
		$pagetype = $_GET['page'];
	}else{
		header("Location: admin.php?page=content");
	}

  if (isset($_GET['edit']))
	{
		$edit = $_GET['edit'];
	}else{
    $edit = 'false';
  }

  

?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Hugo 0.88.1">
    <title>Dashboard Template · Bootstrap v5.1</title>

<?php
    if($pagetype =='essay-edit')
  {
    ?>
<!-- Main Quill library -->
<script src="//cdn.quilljs.com/1.3.6/quill.js"></script>
<script src="//cdn.quilljs.com/1.3.6/quill.min.js"></script>

<!-- Theme included stylesheets -->
<link href="//cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<link href="//cdn.quilljs.com/1.3.6/quill.bubble.css" rel="stylesheet">

<!-- Core build with no theme, formatting, non-essential modules -->
<link href="//cdn.quilljs.com/1.3.6/quill.core.css" rel="stylesheet">
<script src="//cdn.quilljs.com/1.3.6/quill.core.js"></script>
</script>
  <?php
  }
  ?>   

    <!-- Bootstrap core CSS -->
<link href="assets/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.0/font/bootstrap-icons.css">

    <style>
      .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        user-select: none;
      }

      @media (min-width: 768px) {
        .bd-placeholder-img-lg {
          font-size: 3.5rem;
        }
      }
    </style>

    
    <!-- Custom styles for this template -->
    <link href="assets/dist/css/dashboard.css" rel="stylesheet">
  </head>
  <body>
    
<header class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow">
  <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3" href="admin.php"><?php echo $ini['app_name']; ?></a>
  <ul class="nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0">
    <?php
      if(str_contains($pagetype,'essay') || str_contains($pagetype,'content') || str_contains($pagetype,'album'))
      {
    ?>
      <li><a href="admin.php?page=content" class="nav-link px-2 link-light">Content</a></li>
      <li><a href="admin.php?page=site-information" class="nav-link px-2 link-secondary">Settings</a></li>
    <?php
      }else
      {
    ?>
      <li><a href="admin.php?page=content" class="nav-link px-2 link-secondary">Content</a></li>
      <li><a href="admin.php?page=site-information" class="nav-link px-2 link-light">Settings</a></li>
    <?php
      }
    ?>
      </ul>
      <div class="col-md-3 text-end">        
        <div class="nav-item text-nowrap">
          <a class="nav-link px-3" href="login.php?login=logoff">Sign out</a>
        </div>
      </div>
  <button class="navbar-toggler position-absolute d-md-none collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
</header>
<?php
if($pagetype == 'essay' || $pagetype == 'essay-edit' || $pagetype == 'essay-detail' || $pagetype == 'content' || $pagetype == 'album')
{
  ?>
<div class="container-fluid">
  <div class="row">
    <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
      <div class="position-sticky pt-3">
        <ul class="nav flex-column">
          <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="#">
              <span data-feather="home"></span>
              Dashboard
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="admin.php?page=album">
              <span data-feather="file"></span>
              Albums
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="admin.php?page=essay">
              <span data-feather="shopping-cart"></span>
              Essays
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="admin.php?page=content">
              <span data-feather="users"></span>
              Content
            </a>
          </li>          
        </ul>
        
        <?php
}elseif($pagetype == 'site-information' || $pagetype == 'site-settings')
{
  ?>
  <div class="container-fluid">
  <div class="row">
    <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
      <div class="position-sticky pt-3">
        <ul class="nav flex-column">
          <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="admin.php?page=site-information">
              <span data-feather="home"></span>
              Site Information
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="admin.php?page=site-settings">
              <span data-feather="file"></span>
              Site Settings
            </a>
          </li>         
        </ul>
<?php
}

  if($pagetype =='essay')
  {
    include 'gui\essay.php';
  }elseif($pagetype =='essay-detail')
  {
    include 'gui\essay-detail.php';
  }elseif($pagetype =='essay-edit')
  {
    include 'gui\essay-edit.php';
  }elseif($pagetype =='content')
  {
    include 'gui\content.php';
  }elseif($pagetype =='album')
  {
    include 'gui\album.php';
  }elseif($pagetype =='site-information')
  {
    include 'gui\site-information.php';
  }elseif($pagetype =='site-settings')
  {
    include 'gui\site-settings.php';
  }else{
    include 'gui\dashboard.php';
  }
?>
  </div>
</div>
    <script src="assets/dist/js/bootstrap.bundle.min.js"></script>
    <?php
    if($pagetype =='essay-edit')
  {
    ?>
<!-- Include the Quill library -->
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>

<!-- Initialize Quill editor -->
<script>
  var quill = new Quill('#editor', {
    theme: 'snow'
  });
</script>
  <?php
  }
  ?> 

      <script src="https://cdn.jsdelivr.net/npm/feather-icons@4.28.0/dist/feather.min.js" integrity="sha384-uO3SXW5IuS1ZpFPKugNNWqTZRRglnUJK6UAZ/gxOX80nxEkN9NcGZTftn6RzhGWE" crossorigin="anonymous"></script><script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js" integrity="sha384-zNy6FEbO50N+Cg5wap8IKA4M/ZnLJgzc6w2NqACZaK0u0FXfOWRRJOnQtpZun8ha" crossorigin="anonymous"></script><script src="dashboard.js"></script>
  </body>
</html>
