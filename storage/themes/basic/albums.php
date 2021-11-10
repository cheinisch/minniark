<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">
    <!-- anfang -->

        <?php
        pcs_albums_item('
        <div class="col">
          <div class="card shadow-sm">
            <img class="card-img-top" width="100%" height="225" src="{{thumbnail}}">
            

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