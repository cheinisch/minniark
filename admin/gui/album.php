<?php

if (isset($_GET['page']))
{
  if($_GET['page'] == 'album-update')
  {
      echo $_POST['title'];
      echo $_POST['content'];

      update_album_descripton($_POST['title'], $_POST['content'], $_GET['id']);

      header("Location: admin.php?page=album-edit&id=".$_GET['id']);

  }elseif($_GET['page'] == 'album-create')
  {
      echo $_POST['title'];
      echo $_POST['content'];

      create_album($_POST['title'], $_POST['content']);

  }elseif($_GET['page'] == 'album-edit')
  {
?>
</div>
</nav>
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
  <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Album</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
      <div class="btn-group me-2">
        <form action="admin.php?page=album-update&id=<?php echo $_GET['id']; ?>" method="post">
        <button type="submit" class="btn btn-sm btn-outline-danger">Save</button>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-lg-4">
      <div class="form-group">
        <label for="title">Title:</label>
        <input type="text" id="title" placeholder="Title" autocomplete="off" class="form-control" name="title" value="<?php echo ip_get_album_title(); ?>"/>
      </div>
      <label for="content">Content:</label> 
      <textarea name="content" id="editor">
        <?php echo ip_get_album_description(); ?>
      </textarea>
          </form>
      <script>
        ClassicEditor
        .create( document.querySelector( '#editor' ) )
        .catch( error => {
        console.error( error );
        } );
      </script>
<!-- anfang -->

<?php
  }elseif( $_GET['page'] == 'album-new')
  {
?>
</div>
</nav>
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
  <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Album</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
      <div class="btn-group me-2">
        <form action="admin.php?page=album-create" method="post">
        <button type="submit" class="btn btn-sm btn-outline-danger">Save</button>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-lg-4">
      <div class="form-group">
        <label for="title">Title:</label>
        <input type="text" id="title" placeholder="Title" autocomplete="off" class="form-control" name="title" value="Albumtitle"/>
      </div>
      <label for="content">Content:</label> 
      <textarea name="content" id="editor">
        Album Text
      </textarea>
          </form>
      <script>
        ClassicEditor
        .create( document.querySelector( '#editor' ) )
        .catch( error => {
        console.error( error );
        } );
      </script>
<!-- anfang -->

<?php
  }elseif($_GET['page'] == 'album')
  {
?>    
  

<h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
          <span>Album List</span>
          <a class="link-secondary" href="#" aria-label="Add a new report">
            <span data-feather="plus-circle"></span>
          </a>
        </h6>
        <ul class="nav flex-column mb-2">
          <li class="nav-item">
            <a class="nav-link" href="#">
              <span data-feather="file-text"></span>
              Album 1
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#">
              <span data-feather="file-text"></span>
              Album 2
            </a>
          </li>
        </ul>
        </div>
    </nav>
    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
      <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Album</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
          <div class="btn-group me-2">
            <a href="admin.php?page=album-new" class="btn btn-sm btn-outline-secondary">New</a>
          </div>
        </div>
      </div>
      <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">
    <!-- anfang -->

        <?php
        pcs_admin_albums_item('
        <div class="col-lg-2">
          <div class="card shadow-sm">
            <img class="card-img-top" width="100%" height="225" src="{{image.thumbnail}}">
            

            <div class="card-body">
              <p class="card-text"><a href="{{album_url}}">{{text}}</a></p>
              <div class="d-flex justify-content-between align-items-center">
                <small class="text-muted">{{date}}</small>
              </div>
            </div>
          </div>
        </div>
    ')   ;
        ?>
        <!-- ende -->
        
</div>
    </main>

    <?php 

}
}

?>
