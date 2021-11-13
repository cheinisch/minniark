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
            <button type="button" class="btn btn-sm btn-outline-secondary">New</button>
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
