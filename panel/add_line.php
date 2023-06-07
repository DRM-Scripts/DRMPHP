<?php

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

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $expire_date = $_POST['expire_date'];

    // Generate random username and password if empty
    if (empty($username)) {
        $username = generateRandomString(15);
    }
    if (empty($password)) {
        $password = generateRandomString(15);
    }

    // Insert new line into the database
    $stmt = $db->prepare("INSERT INTO `lines` (`username`, `password`, `expire_date`) VALUES (?, ?, ?)");
    $stmt->execute([$username, $password, $expire_date]);

    // Redirect to lines.php after adding the line
    header("Location: lines.php");
    exit;
}

// Function to generate a random string of alphanumeric characters
function generateRandomString($length) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
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
                                    <li class="breadcrumb-item active">Add Line</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title mb-4">Add Line</h4>

                                <div class="form-container">
                                    <form action="" method="POST">
                                        <div class="mb-3">
                                            <label for="username" class="form-label">Username</label>
                                            <input type="text" class="form-control" id="username" name="username" value="">
                                        </div>
                                        <div class="mb-3">
                                            <label for="password" class="form-label">Password</label>
                                            <input type="text" class="form-control" id="password" name="password" value="">
                                        </div>
                                        <div class="mb-3">
                                            <label for="expire_date" class="form-label">Expire Date</label>
                                            <input type="text" class="form-control" id="expire_date" name="expire_date" required>
                                        </div>
                                        <div class="text-center">
                                            <button type="submit" class="btn btn-primary">Add Line</button>
                                        </div>
                                    </form>
                                </div>
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

<!-- Add the JavaScript code for line deletion here -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.0/jquery-ui.min.js"></script>
<script>
    // Example JavaScript code for line deletion
    var deleteButtons = document.querySelectorAll('.delete-line');
    deleteButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            var lineId = this.getAttribute('data-line-id');
            // Implement the deletion logic here
        });
    });

    // Datepicker
    $(function() {
        $("#expire_date").datepicker({
            dateFormat: 'yy-mm-dd',
            changeMonth: true,
            changeYear: true
        });
    });
</script>
</body>
</html>
