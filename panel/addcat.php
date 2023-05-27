<?php
include "_config.php";
if(!$App->LoggedIn())header('location: login.php');
$Err1="";
$Err2="";
if(isset($_POST["Save"]) && $_POST["Save"]==1){
  $CatID=$App->SaveCat($_POST);
  $Data=$App->GetCat($CatID);
}else{
  $CatID = intval($_POST["ID"]);
  if($CatID == 0){
    $CatID = intval($_GET["id"]);
  }
  if($CatID > 0){
    $Data=$App->GetCat($CatID);
  }else{
    $Data=$_POST;
  }
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
                  <h4 class="mb-sm-0 font-size-18">
                    <?php if($CatID){ ?>
                      Edit Category: <span class="bg-light"><?php
echo $Data["CatName"];
?></span>
                    <?php }else{?>
                      Add New Category
                    <?php }?>
                  </h4>

                  <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                      <li class="breadcrumb-item"><a href="javascript: void(0);">Main</a></li>
                      <li class="breadcrumb-item active">
                        <?php if($CatID){ ?>
                          Edit Category
                        <?php }else{?>
                          Add new Category
                        <?php }?>
                      </li>
                    </ol>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-12">
                <a href="cats.php" style="float:right;" class="mb-3 btn btn-light waves-effect btn-label waves-light"><i class="bx bxs-left-arrow-circle label-icon"></i> Back to list</a>
              </div>
            </div>


            <form method="POST">
              <div class="row">
                <div class="col-lg-6">
                  <div class="card">
                    <div class="card-body">
                      <h4 class="card-title">Category information</h4>
                      <div class="mb-3 row">
                        <label class="col-md-2 col-form-label">Name</label>
                        <div class="col-md-10">
                          <input class="form-control" type="text" id="CatName" name="CatName" value="<?php
echo $Data["CatName"];
?>">
                        </div>
                      </div>
                                            

                      <div class="mb-3 row">
                        <label class="col-md-2 col-form-label"></label>
                        <div class="col-md-10">
                          <button type="submit" class="btn btn-success waves-effect btn-label waves-light"><i class="bx bxs-save label-icon"></i> Save</button>
                          <a href="cats.php" class="btn btn-light waves-effect btn-label waves-light"><i class="bx bx-undo label-icon"></i> Cancel</a>
                        </div>
                      </div>

                    </div>
                  </div>
                </div>
              </div>
              <input type="hidden" name="Save" value="1">
              <input type="hidden" name="ID" value="<?php
echo $CatID;
?>">
            </form>
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