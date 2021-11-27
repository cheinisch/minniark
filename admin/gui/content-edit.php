</div>
    </nav>
    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
  <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Pictures</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
      <div class="btn-group me-2">
        <form method='post' action='' enctype='multipart/form-data'>
 <input type="file" name="file[]" class="btn btn-sm btn-outline-danger" id="file" multiple>

 <input type='submit' name='submit' class="btn btn-sm btn-outline-danger" value='Upload'>
</form>
      </div>
    </div>
  </div>
  <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">
<?php 
$picturelist = get_pictures();
while($row = $picturelist->fetch_assoc())
{
?>
<div class="col-lg-2">
  <div class="card shadow-sm">
    <a href="admin.php?page=content-edit&id=<?php echo $row["id"] ?>"><img class="card-img-top" width="100%" style="max-height: 255px;" src="../storage/images/cache/thumb_<?php echo $row["content-filename"] ?>"></a>
  </div>
</div>
<?php
}
?>
  </div>
</main>
