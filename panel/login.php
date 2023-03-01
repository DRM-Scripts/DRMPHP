<?php
include "_config.php";
$_SESSION["User"]=null;
unset($_SESSION["User"]);
$Msg="";
if(isset($_POST["login"]) && $_POST["login"]==1){
  $UserID= $_POST["UserID"];
  $Password= $_POST["Password"];
  $User = $App->Login($UserID, $Password);
  if($User["ID"] > 0 ){
    $_SESSION["User"] = $User;
    header('location: dashboard.php');
  }else{
    $_SESSION["User"] = null;
    $Msg="Invalid UserID/Password";
  }
}
?>
<!doctype html>
<html lang="en">
  <?php include "_htmlhead.php"?>
  <body>
    <div class="account-pages my-5 pt-sm-5">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-md-8 col-lg-6 col-xl-5">
            <div class="card overflow-hidden">
              <div class="bg-primary bg-soft">
                <div class="row">
                  <div class="col-7">
                    <div class="text-primary p-4">
                      <h5 class="text-primary">Welcome !</h5>
                      <p>Sign in to Dash-Admin.</p>
                    </div>
                  </div>
                  <div class="col-5 align-self-center">
                    <img src="assets/images/logo.png" alt="" class="img-fluid">
                  </div>
                </div>
              </div>
              <div class="card-body pt-0"> 
                <div class="auth-logo">
                  <a href="javascript: void(0)" class="auth-logo-light">
                    <div class="avatar-md profile-user-wid mb-4">
                      <span class="avatar-title bg-light">
                        <img src="assets/images/logo.png" alt="" class="" height="34">
                      </span>
                    </div>
                  </a>
                </div>
                <div class="p-2">
                  <form class="form-horizontal" method="POST">

                    <div class="mb-3">
                      <label for="username" class="form-label">Username</label>
                      <input type="text" class="form-control" id="UserID" name="UserID" placeholder="Enter username">
                    </div>

                    <div class="mb-3">
                      <label class="form-label">Password</label>
                      <div class="input-group auth-pass-inputgroup">
                        <input type="password" class="form-control" id="Password" Name="Password" placeholder="Enter password" aria-label="Password" aria-describedby="password-addon">
                        <button class="btn btn-light " type="button" id="password-addon"><i class="mdi mdi-eye-outline"></i></button>
                      </div>
                    </div>

                    <div class="mt-3 d-grid">
                      <button class="btn btn-outline-dark waves-effect waves-light" type="submit">Log In</button>
                    </div>

                    <div class="mt-4 text-center">
                      <a href="javascript: void(0)" class="text-muted"><i class="mdi mdi-lock me-1"></i> <script>document.write(new Date().getFullYear())</script> © Dash Admin.</a>
                    </div>
                    <input type="hidden" name="login" value="1"> 
                  </form>
                </div>

              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <script src="assets/libs/jquery/jquery.min.js"></script>
    <script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/libs/metismenu/metisMenu.min.js"></script>
    <script src="assets/libs/simplebar/simplebar.min.js"></script>
    <script src="assets/libs/node-waves/waves.min.js"></script>
    <script src="assets/js/app.js"></script>
  </body>
</html>
