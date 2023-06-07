<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include "_config.php";
include "_db.php";

try {
    $db = new PDO('mysql:host='.$DBHost.';dbname='.$DBName.';charset=utf8', $DBUser, $DBPass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    print "Error!: " . $e->getMessage() . "<br/>";
    die();
}

// Fetch all lines from the database
$lines = $db->query("SELECT * FROM `lines`")->fetchAll(PDO::FETCH_ASSOC);

// Display error message if the database connection is not available
$db ??= null;
if ($db === null) {
    echo 'Failed to establish a database connection.';
    exit;
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
                  <h4 class="mb-sm-0 font-size-18">Lines</h4>

                  <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                      <li class="breadcrumb-item"><a href="javascript: void(0);">Main</a></li>
                      <li class="breadcrumb-item active">Lines</li>
                    </ol>
                  </div>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-lg-12">
                <div class="card">
                  <div class="card-body">
                    <h4 class="card-title mb-4">Line List</h4>

                    <?php if (count($lines) > 0): ?>
                      <table class="table table-bordered table-sm">
                        <thead>
                          <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Password</th>
                            <th>Expire Date</th>
                            <th>Actions</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php foreach ($lines as $line): ?>
                            <tr>
                              <td><?php echo $line['id']; ?></td>
                              <td><?php echo $line['username']; ?></td>
                              <td><?php echo $line['password']; ?></td>
                              <td><?php echo $line['expire_date']; ?></td>
                              <td>
                                <a href="edit_line.php?id=<?php echo $line['id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                                <button class="btn btn-danger btn-sm delete-line" data-line-id="<?php echo $line['id']; ?>">Delete</button>
                                <a href="export.php" class="btn btn-success btn-sm download-line"
                                   data-username="<?php echo $line['username']; ?>"
                                   data-password="<?php echo $line['password']; ?>">
                                    <i class="fa fa-download"></i> Download
                                </a>
                              </td>
                            </tr>
                          <?php endforeach; ?>
                        </tbody>
                      </table>
                    <?php else: ?>
                      <p>No lines found.</p>
                    <?php endif; ?>
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

<!-- Add the JavaScript code for line deletion and download here -->
<script>
  // Example JavaScript code for line deletion
  var deleteButtons = document.querySelectorAll('.delete-line');
  deleteButtons.forEach(function(button) {
    button.addEventListener('click', function() {
      var lineId = this.getAttribute('data-line-id');
      // Implement the deletion logic here
    });
  });

  // JavaScript code for download button
  var downloadButtons = document.querySelectorAll('.download-line');
  downloadButtons.forEach(function(button) {
      button.addEventListener('click', function(event) {
          event.preventDefault();
          var username = this.getAttribute('data-username');
          var password = this.getAttribute('data-password');

          var form = document.createElement('form');
          form.setAttribute('method', 'POST');
          form.setAttribute('action', 'export.php');
          form.style.display = 'none';

          var usernameInput = document.createElement('input');
          usernameInput.setAttribute('type', 'hidden');
          usernameInput.setAttribute('name', 'username');
          usernameInput.setAttribute('value', username);
          form.appendChild(usernameInput);

          var passwordInput = document.createElement('input');
          passwordInput.setAttribute('type', 'hidden');
          passwordInput.setAttribute('name', 'password');
          passwordInput.setAttribute('value', password);
          form.appendChild(passwordInput);

          var typeInput = document.createElement('input');
          typeInput.setAttribute('type', 'hidden');
          typeInput.setAttribute('name', 'type');
          typeInput.setAttribute('value', 'hls');
          form.appendChild(typeInput);

          document.body.appendChild(form);
          form.submit();
      });
  });
</script>


  </body>
</html>
