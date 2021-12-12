<?php

  $userdata = get_userdata();

  if(isset($_GET['change']))
  {
    if($_GET['change'] == "true")
    {
      if(empty($_POST["userpasswd"]))
      {
        $passwd = null;
      }else{
        $passwd = $_POST["userpasswd"];
      }
      set_userdata($_POST["username"],$_POST["usermail"],$passwd, $userdata["admin_user"]);
    }

    header("Location: admin.php?page=user-settings");
  }

?>

</div>
    </nav>
    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
        <form action="admin.php?page=user-settings&change=true" method="post">
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
    <input type="text" class="form-control" id="exampleInputPassword1" placeholder="Username" name="username" value="<?php echo $userdata["admin_user"]; ?>">
  </div>
  <div class="form-group">
    <label for="exampleInputEmail1">Email address</label>
    <input type="email" class="form-control" id="exampleInputEmail1" placeholder="Enter email" name="usermail" value="<?php echo $userdata["admin_mail"]; ?>">
  </div>
  <div class="form-group">
    <label for="exampleInputPassword1">Password</label>
    <input type="password" class="form-control" id="exampleInputPassword1" name="userpasswd" placeholder="Please leave field empty, if no change">
  </div>
  <div class="form-group">
    <label for="exampleInputPassword1">Confirm Password</label>
    <input type="password" class="form-control" id="exampleInputPassword1" placeholder="Please leave field empty, if no change">
  </div>
</div>
</div>
</form>
</main>