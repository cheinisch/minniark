<?php

  $sitedata = get_sitedata();

  if(isset($_GET['change']))
  {
    if($_GET['change'] == "true")
    {
      set_sitedata($_POST["sitename"],$_POST["sitetitle"],$_POST["sitetagline"],$_POST["sitecopyright"],$_POST["sitekeywords"], $sitedata["admin_user"]);
    }

    header("Location: admin.php?page=site-settings");
  }elseif(isset($_GET['cache']))
  {
    if($_GET['cache'] == "true")
    {
      recreate_cache();
    }
    

    header("Location: admin.php?page=site-settings");
  }

?>

</div>
    </nav>
    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
      <form action="admin.php?page=site-settings&change=true" method="post">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
          <h1 class="h2">Site Settings</h1>
          <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
              <button type="submit" class="btn btn-sm btn-outline-danger">Save</button>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-lg-4">
            <div class="form-group">
                <label for="InputName">Site Name</label>
                <input type="text" class="form-control" id="InputName" placeholder="Site Name" name="sitename" value="<?php echo $sitedata["site-name"]; ?>">
              </div>
              <div class="form-group">
                <label for="InputTitle">Site Title</label>
                <input type="text" class="form-control" id="InputTitle" placeholder="Site Title" name="sitetitle" value="<?php echo $sitedata["site-title"]; ?>">
              </div>
              <div class="form-group">
                <label for="InputTagline">Tagline</label>
                <input type="text" class="form-control" id="InputTagline" placeholder="Tagline" name="sitetagline" value="<?php echo $sitedata["site-tagline"]; ?>">
              </div>
              <div class="form-group">
                <label for="InputCopyright">Copyright</label>
                <input type="text" class="form-control" id="InputCopyright" placeholder="Copyright" name="sitecopyright" value="<?php echo $sitedata["site-copyright"]; ?>">
              </div>
              <div class="form-group">
                <label for="InputKeywords">Keywords</label>
                <input type="text" class="form-control" id="InputKeywords" placeholder="Keywords" name="sitekeywords" value="<?php echo $sitedata["site-keywords"]; ?>">
              </div>
            </div>
          </div>
      
      <div class="row">
        <div class="col-lg-4">
          <a href="admin.php?page=site-settings&cache=true" class="btn btn-primary">Recreate Cache</a>
        </div>
</div>
      </form>
    </main>