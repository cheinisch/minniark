</div>
    </nav>
    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
  <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Picture</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
      <div class="btn-group me-2">
      <form>
      <button type="submit" class="btn btn-sm btn-outline-danger">Save</button>
      </div>
    </div>
  </div>
  <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">
    <div class="col-lg-6">
      <?php

        $image_data = get_image();
      ?>
        <img class="card-img-top" width="100%" src="../storage/images/cache/medium_<?php echo $image_data["content-filename"]; ?>">
    </div>
   
      <div class="col-lg-4">
        <div class="form-group">
          <label for="InputName">Picture Name</label>
          <input type="text" class="form-control" id="InputName" placeholder="Title" name="Title" value="<?php echo $image_data["content-name"]; ?>">
        </div>
        <div class="form-group">
          <label for="InputTitle">Description</label>
          <textarea class="form-control" id="InputTitle" placeholder="Description" name="Description" value="<?php echo $image_data["content-text"]; ?>" rows="3"></textarea>
        </div>
        <?php
        $albumlist = get_albums();
 
        while($row = $albumlist->fetch_assoc())
        {
          if(is_picture_in_album($_GET['id'],$row['id']))
          {
            $checked = "checked";
          }else{
            $checked = "";
          }

          ?>
        <div class="form-check">
          <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault" <?php echo $checked; ?>>
          <label class="form-check-label" for="flexCheckDefault">
          <?php echo $row['content_title']; ?>
          </label>
        </div>
        <?php
        }
        ?>
      </div>
    </form>
  </div>
  <hr>
  <div class="row">
    
  </div>
</main>
