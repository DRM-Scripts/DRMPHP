<?php
include "_config.php";
if (!$App->LoggedIn()) header('location: login.php');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  // Process password change form submission
  // Add your password change logic here
  // Retrieve and validate the new password
  $newPassword = $_POST['new_password'];
  $confirmPassword = $_POST['confirm_password'];

  // Check if the new password and confirmation match
  if ($newPassword === $confirmPassword) {
    // Passwords match, proceed with password change
    $currentUserId = null; // Default value if session variable is not set

if (isset($_SESSION['User']['UserID'])) {
    $currentUserId = $_SESSION['User']['UserID'];
}

    $currentPassword = $_POST['current_password']; // Retrieve the current password

    // Call the ChangePassword method using the App instance
    $passwordChanged = $App->ChangePassword($currentUserId, $currentPassword, $newPassword);

    if ($passwordChanged) {
      // Password change successful, redirect to a success page
      header('location: password_changed.php');
      exit;
    } else {
      // Display an error message if password change fails
      $errorMessage = "Failed to change password. Please make sure your current password is correct.";
    }
  } else {
    // Passwords do not match, display an error message
    $errorMessage = "Passwords do not match.";
  }
}
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
                  <h4 class="mb-sm-0 font-size-18">Change Password</h4>

                  <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                      <li class="breadcrumb-item"><a href="javascript: void(0);">Main</a></li>
                      <li class="breadcrumb-item active">Change Password</li>
                    </ol>
                  </div>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-lg-6">
                <div class="card">
                  <div class="card-body">
                    <h4 class="card-title mb-4">Change Your Password</h4>

                    <?php if (isset($errorMessage)): ?>
                      <div class="alert alert-danger" role="alert">
                        <?php echo $errorMessage; ?>
                      </div>
                    <?php endif; ?>

                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                      <div class="mb-3">
                        <label for="current_password" class="form-label">Current Password</label>
                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                      </div>

                      <div class="mb-3">
                        <label for="new_password" class="form-label">New Password</label>
                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                      </div>

                      <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                      </div>

                      <div class="text-end">
                        <button type="submit" class="btn btn-primary">Change Password</button>
                      </div>
                    </form>
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
