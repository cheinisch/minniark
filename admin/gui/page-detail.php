 <?php

 if(isset($_GET['delete']))
 {
   if($_GET['delete'] == 'true')
   {
      delete_page($_GET['id']);
   }
 }

?>
 
 <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
          <span>Pages</span>
          <a class="link-secondary" href="#" aria-label="Add a new report">
            <span data-feather="plus-circle"></span>
          </a>
        </h6>
        <ul class="nav flex-column mb-2">
        <?php
            $pagelist = get_pages();
            while($row = $pagelist->fetch_assoc())
            {
              ?>
              <li class="nav-item">
            <a class="nav-link" href="admin.php?page=page-detail&id=<?php echo $row["id"]; ?>">
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
        <h1 class="h2">Pages</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
          <div class="btn-group me-2">
            <button button type="button" class="btn btn-sm btn-outline-danger" onclick="myFunction()">Delete</button>
            <a href="admin.php?page=page-edit&id=<?php echo $_GET['id']; ?>" type="button" class="btn btn-sm btn-outline-primary">Edit</a>
          </div>
          </div>
      </div>

      <?php
        $row = get_page($_GET['id']);
        $page = $row->fetch_assoc();
      ?>

      <h3><?php echo $page["content_title"]; ?></h3>
      <?php echo $page["content_text"]; ?>
      <div class="border-bottom mb-4"></div>
      <div class="row">
        <div class="col-lg-2">
        </div>
        <div class="col-lg-2">
        </div>
        <div class="col-lg-6">
        
        </div>
        <div class="col-lg-2">
        <div class="btn-group me-2">
          <button type="button" class="btn btn-sm btn-outline-secondary">Draft</button>
          <a href="admin.php?page=page&edit=true" type="button" class="btn btn-sm btn-outline-secondary">Public</a>
          <button type="button" class="btn btn-sm btn-primary">Private</button>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-2">
            Last Update: <?php echo $page["latest_update"]; ?>
        </div>
        <div class="col-lg-2">
        Publish Date: <?php echo $page["publish_date"]; ?> - <a href="#">edit</a>
        </div>
        <div class="col-lg-6">
        
        </div>
        <div class="col-lg-2">

      </div>
    </main>
    <script>
function myFunction() {
  var txt;
  var r = confirm("Are you sure to delete this page?");
  if (r == true) {
    txt = "You pressed OK!";
    window.open('admin.php?page=page-detail&id=<?php echo $_GET['id']; ?>&delete=true');
  } else {
  }
}
</script>
