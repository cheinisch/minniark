<?php

  if(isset($_GET['page']))
  {
    if($_GET['page'] == 'essay-update')
    {
      update_essay($_POST['title'], $_POST['content'], $_GET['id']);
      header("Location: admin.php?page=essay-edit&id=".$_GET['id']);
    }elseif($_GET['page'] == 'essay-create')
    {
      create_essay($_POST['title'], $_POST['content']);
    }elseif($_GET['page'] == 'essay-new')
    {
?>

      
     
<h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
          <span>Essays</span>
          <a class="link-secondary" href="#" aria-label="Add a new report">
            <span data-feather="plus-circle"></span>
          </a>
        </h6>
        <ul class="nav flex-column mb-2">
        <?php
            $essaylist = get_essays();
            while($row = $essaylist->fetch_assoc())
            {
              ?>
              <li class="nav-item">
            <a class="nav-link" href="admin.php?page=essay-detail&id=<?php echo $row["id"]; ?>">
              <span data-feather="file-text"></span>
              <?php echo $row["content_title"]; ?>
            </a>
          </li>
              <?php
            }

        ?>
        </ul>
        </div>
    </nav>
    <main class="col-md-8 ms-sm-auto col-lg-10 px-md-4">
      <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Essays</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
      <div class="btn-group me-2">
        <form action="admin.php?page=essay-create" method="post">
        <button type="submit" class="btn btn-sm btn-outline-danger">Save</button>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-lg-6">
      <div class="form-group">

      <label for="title">Title:</label>
        <input type="text" id="title" placeholder="Title" autocomplete="off" class="form-control" name="title"/>
      </div>
      <label for="content">Content:</label> 
      <textarea name="content" id="editor" placeholder="Essaytext">
      
      </textarea>
          </form>
      <script>
        ClassicEditor
        .create( document.querySelector( '#editor' ) )
        .catch( error => {
        console.error( error );
        } );
      </script>
    </main>

<?php
    }else{
      ?>

      
     
     <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
          <span>Essays</span>
          <a class="link-secondary" href="#" aria-label="Add a new report">
            <span data-feather="plus-circle"></span>
          </a>
        </h6>
        <ul class="nav flex-column mb-2">
        <?php
            $essaylist = get_essays();
            while($row = $essaylist->fetch_assoc())
            {
              ?>
              <li class="nav-item">
            <a class="nav-link" href="admin.php?page=essay-detail&id=<?php echo $row["id"]; ?>">
              <span data-feather="file-text"></span>
              <?php echo $row["content_title"]; ?>
            </a>
          </li>
              <?php
            }

        ?>
        </ul>
        </div>
    </nav>
    <main class="col-md-8 ms-sm-auto col-lg-10 px-md-4">
      <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Essays</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
      <div class="btn-group me-2">
        <form action="admin.php?page=essay-update&id=<?php echo $_GET['id']; ?>" method="post">
        <button type="submit" class="btn btn-sm btn-outline-danger">Save</button>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-lg-6">
      <div class="form-group">

      <?php
        $row = get_essay($_GET['id']);
        $essay = $row->fetch_assoc();
      ?>

      <label for="title">Title:</label>
        <input type="text" id="title" placeholder="Title" autocomplete="off" class="form-control" name="title" value="<?php echo $essay["content_title"]; ?>"/>
      </div>
      <label for="content">Content:</label> 
      <textarea name="content" id="editor">
      <?php echo $essay["content_text"]; ?>
      </textarea>
          </form>
      <script>
        ClassicEditor
        .create( document.querySelector( '#editor' ) )
        .catch( error => {
        console.error( error );
        } );
      </script>
    </main>
    <?php
    }
  }


?>