<?php
include "_config.php";
if (!$App->LoggedIn()) {
    header('location: login.php');
}

if (isset($_POST["DownloadPath"]) && $_POST["DownloadPath"] != "") {
    $App->ChangePassword($_POST);
}
?>
<!doctype html>
<html lang="en">
<?php include "_htmlhead.php"?>

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
                        <div class="col-lg-12">
                            <div class="card-body">
                                <form method="POST">
                                    <div class="mb-3 row">
                                        <label class="col-md-2 col-form-label">Current Password</label>
                                        <div class="col-md-10">
                                            <input class="form-control" type="password" id="currentPassword"
                                                name="currentPassword" value="" required>
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label class="col-md-2 col-form-label">New Password</label>
                                        <div class="col-md-10">
                                            <input class="form-control" type="password" id="newPassword"
                                                name="newPassword" value="" required>
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label class="col-md-2 col-form-label">Confirm New Password</label>
                                        <div class="col-md-10">
                                            <input class="form-control" type="password" id="confirmNewPassword"
                                                name="confirmNewPassword" value="" required>
                                        </div>
                                    </div>
                                    <div class="col-md-10">
                                        <button type="submit"
                                            class="btn btn-success waves-effect btn-label waves-light"><i
                                                class="bx bxs-save label-icon"></i> Save</button>
                                        <a href="index.php" class="btn btn-light waves-effect btn-label waves-light"><i
                                                class="bx bx-undo label-icon"></i> Cancel</a>
                                    </div>
                                </form>
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
    <script>
    function DownloadBackup(file) {
        $.post("_app.php", {
                Func: "DownloadBackup",
                File: file
            })
            .done(function() {
                window.open('getbkup.php?file=' + file, '_blank').focus();
            })
    }
    </script>
</body>

</html>