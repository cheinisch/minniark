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
              <?php echo $row["essay_title"]; ?>
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
            <button type="button" class="btn btn-sm btn-outline-danger">Delete</button>
            <a href="admin.php?page=essay-edit" type="button" class="btn btn-sm btn-outline-primary">Edit</a>
          </div>
          </div>
      </div>

      <?php
        $row = get_essay($_GET['id']);
        $essay = $row->fetch_assoc();
      ?>

      <h3><?php echo $essay["essay_title"]; ?></h3>
      <?php echo $essay["essay_text"]; ?>
      <div class="border-bottom mb-4"></div>
      <div class="row">
        <div class="col-lg-2">
            Category: cat-name - <a href="#">edit</a>
        </div>
        <div class="col-lg-2">
        Topic: cat-name - <a href="#">edit</a>
        </div>
        <div class="col-lg-6">
        
        </div>
        <div class="col-lg-2">
        <div class="btn-group me-2">
          <button type="button" class="btn btn-sm btn-outline-secondary">Draft</button>
          <a href="admin.php?page=essay&edit=true" type="button" class="btn btn-sm btn-outline-secondary">Public</a>
          <button type="button" class="btn btn-sm btn-primary">Private</button>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-2">
            Last Update: <?php echo $essay["latest_update"]; ?>
        </div>
        <div class="col-lg-2">
        Publish Date: <?php echo $essay["publish_date"]; ?> - <a href="#">edit</a>
        </div>
        <div class="col-lg-6">
        
        </div>
        <div class="col-lg-2">

      </div>
    </main>
