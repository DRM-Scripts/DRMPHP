<?php
include "_config.php";
if(!$App->LoggedIn())header('location: login.php');
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
                  <h4 class="mb-sm-0 font-size-18">Categories</h4>

                  <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                      <li class="breadcrumb-item"><a href="javascript: void(0);">Main</a></li>
                      <li class="breadcrumb-item active">Categories List</li>
                    </ol>
                  </div>

                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-12">
                <a href="addcat.php" style="float:right" class="mb-3 btn btn-light waves-effect btn-label waves-light"><i class="bx bx-list-plus label-icon"></i> Add New</a>
              </div>
            </div>
            <div class="row">
              <div class="col-lg-12">
                <div class="card">
                  <div class="card-body">
                    <div class="table-responsive">
                      <table class="table align-middle table-nowrap mb-0">
                        <thead class="table-light">
                          <tr>
                            <th class="align-middle">ID</th>
                            <th class="align-middle" style="width:450px;overflow-wrap: break-word;">Name</th>
                            <th class="align-middle">Channels</th>
                            <th class="align-middle">Action</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php
                          $Data=$App->GetAllCats();
                          if($Data){
                            for($i=0;$i<count($Data);$i++){
                              $Cat=$Data[$i];
                          ?>
                              <tr>
                                <td><?php
echo $Cat["CatID"];
?></td>
                                <td style="width:450px;overflow-wrap: break-word;word-break: break-all;">
                                  <a href="addcat.php?id=<?php
echo $Cat["CatID"]?>" class="text-body fw-bold"><?=$Cat["CatName"];
?></a> 
                                </td>
                                <td><?php
echo $Cat["ChannelsCount"];
?></td>
                                <td>
                                  <div class="btn-group btn-group-sm">
                                    <a class="btn btn-outline-dark" href="addcat.php?id=<?php
echo $Cat["CatID"];
?>"><i class="bx bxs-edit-alt"></i></a>
                                  </div>
                                  <a class="btn btn-danger btn-sm" href="javascript: void(0)" onclick="DeleteCat('<?php
echo $Cat["CatID"];
?>')"><i class="bx bx-trash"></i></a>
                                </td>
                              </tr>
                            <?}
                          }else{
                            ?>
                            <tr>
                              <td colspan="4" class="text-center">
                                <strong>No Categories Found</strong>
                              </td>
                            </tr>
                          <?php
                          }?>
                        </tbody>
                      </table>
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

    <script src="assets/libs/jquery/jquery.min.js"></script>
    <script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/libs/metismenu/metisMenu.min.js"></script>
    <script src="assets/libs/simplebar/simplebar.min.js"></script>
    <script src="assets/libs/node-waves/waves.min.js"></script>
    <script src="assets/js/app.js"></script>
    <script>
      function DeleteCat(id){
        if (confirm("Are you sure to delete this category?") == true) {
          $.post("_func.php", {Func:"DeleteCat", ID:id})
          .done(function(){
            window.location.reload();
          })
        }
      }
    </script>
  </body>
</html>