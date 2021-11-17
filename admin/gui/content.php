<?php 
if(isset($_POST['submit'])){
 
 // Count total files
 $countfiles = count($_FILES['file']['name']);

 // Looping all files
 for($i=0;$i<$countfiles;$i++){
  $filename = $_FILES['file']['name'][$i];
 
  // Upload file
  move_uploaded_file($_FILES['file']['tmp_name'][$i],'../storage/images/original/'.$filename);
 
 }
} 
?>

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
</main>
