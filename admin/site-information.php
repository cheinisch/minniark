<?php

    $ini = parse_ini_file('app.ini');

?>

</div>
    </nav>
    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
      <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Site Information</h1>
        
      </div>
      <h3>Site Info</h3>
      <p>Site Version: <i><?php echo $ini['app_version']; ?></i></p>
      <h3>Server Info</h3>
      <p>PHP Version: <i><?php echo phpversion(); ?></i></p>
    </main>
