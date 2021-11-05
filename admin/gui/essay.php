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
      </div>

      <ul class="nav flex-column mb-2">
        <?php
            $essaylist = get_essays();
            while($row = $essaylist->fetch_assoc())
            {
              ?>
              <div class="row">
      <h3><a href="admin.php?page=essay-detail&id=<?php echo $row["id"]; ?>"><?php echo $row["essay_title"]; ?></a></h3>
      <p>
            <?php
            $string		=	$row["essay_text"]; // Text
            $length		=	512; // Zeichenlänge
            
            $string		=	preg_replace( '/[^ ]*$/', '', substr( $string, 0, $length ) ) . ' ...';
            
            echo $string; ?>
            </p>
            </div>
              <?php
            }

        ?>
      </div>
    </main>
