<?php

  $userdata = get_userdata();

?>

</div>
    </nav>
    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
        <form>
      <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">User Settings</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
          <div class="btn-group me-2">
            <button type="submit" class="btn btn-sm btn-outline-danger">Save</button>
          </div>
          </div>
      </div>
    <div class="row">
        <div class="col-lg-4">
<div class="form-group">
    <label for="exampleInputPassword1">Username</label>
    <input type="text" class="form-control" id="exampleInputPassword1" placeholder="Username" value="<?php echo $userdata["admin_user"]; ?>">
  </div>
  <div class="form-group">
    <label for="exampleInputEmail1">Email address</label>
    <input type="email" class="form-control" id="exampleInputEmail1" placeholder="Enter email" value="<?php echo $userdata["admin_mail"]; ?>">
  </div>
  <div class="form-group">
    <label for="exampleInputPassword1">Password</label>
    <input type="password" class="form-control" id="exampleInputPassword1" placeholder="Please leave field empty, if no change">
  </div>
  <div class="form-group">
    <label for="exampleInputPassword1">Confirm Password</label>
    <input type="password" class="form-control" id="exampleInputPassword1" placeholder="Please leave field empty, if no change">
  </div>
</div>
</div>
</form>
</main>