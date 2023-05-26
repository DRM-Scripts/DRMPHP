<?php
include "_config.php";
if(!$App->LoggedIn())header('location: login.php');
?>
<!doctype html>
<html lang="en">
  <?php include "_htmlhead.php"?>
  <style>
    /* Your custom styles here */
  </style>
  <body data-sidebar="dark">
    <div id="layout-wrapper">
      <?php include "_header.php"?>
      <?php include "_sidebar.php"?>

      <div class="main-content">
        <div class="page-content">
          <div class="container-fluid">
            <div class="row">
              <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                  <h4 class="mb-sm-0 font-size-18">Password Changed</h4>

                  <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                      <li class="breadcrumb-item"><a href="javascript: void(0);">Main</a></li>
                      <li class="breadcrumb-item active">Password Changed</li>
                    </ol>
                  </div>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-lg-6">
                <div class="card">
                  <div class="card-body">
                    <h4 class="card-title mb-4">Success</h4>
                    <p>Your password has been successfully changed.</p>
                    <p><a href="dashboard.php">Go back to the dashboard</a></p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <?php include "_footer.php"?>
      </div>
    </div>

    <?php include "_rightbar.php"?>
    <div class="rightbar-overlay"></div>

    <script src="assets/libs/jquery/jquery.min.js"></script>
    <script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/libs/metismenu/metisMenu.min.js"></script>
    <script src="assets/libs/simplebar/simplebar.min.js"></script>
    <script src="assets/libs/node-waves/waves.min.js"></script>
    <script src="assets/js/app.js"></script>
  </body>
</html>