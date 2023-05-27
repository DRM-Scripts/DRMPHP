<?php
include "_config.php";
if(!$App->LoggedIn())header('location: login.php');
$ID=$_GET["id"];
$Lines=$_GET["l"];
$Log=$App->GetLog($ID, $Lines);
$FFMpegLog=$Log[0];
$PHPLog=$Log[1];

$Chan = $App->GetChannel($ID);
$ChName=str_replace(" ","_", $Chan["ChannelName"]);
$WorkPath=$App->GetConfig("DownloadPath");
$x=glob($WorkPath."/".$ChName."/log/ffmpeg*.log");
$LogName1=$x[0];
$x=glob($WorkPath."/".$ChName."/log/php.log");
$LogName2=$x[0];
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
                  <h4 class="mb-sm-0 font-size-18">Streaming Log</h4>

                  <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                      <li class="breadcrumb-item"><a href="javascript: void(0);">Main</a></li>
                      <li class="breadcrumb-item active">Logging</li>
                    </ol>
                  </div>

                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-12">
                <a href="index.php" style="float:right;" class="mb-3 btn btn-light waves-effect btn-label waves-light"><i class="bx bxs-left-arrow-circle label-icon"></i> Back to list</a>
              </div>
            </div>


            <div class="row">
              <div class="col-lg-12">
                <div class="card">
                  <div class="card-body">
                    <div class="nav-item" style="float:right" >
                      Lines: <select id="Lines" onchange="ReloadPage()">
                        <?php if($Lines==20)$Selected="selected";else $Selected="";?>
                        <option <?php
echo $Selected;
?> value="20">20</option>
                        <?php if($Lines==50)$Selected="selected";else $Selected="";?>
                        <option <?php
echo $Selected;
?> value="50">50</option>
                        <?php if($Lines==100)$Selected="selected";else $Selected="";?>
                        <option <?php
echo $Selected;
?> value="100">100</option>
                        <?php if($Lines==500)$Selected="selected";else $Selected="";?>
                        <option <?php
echo $Selected;
?> value="500">500</option>
                      </select>
                    </div>                  
                    <ul class="nav nav-tabs" role="tablist">
                      <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#home" role="tab" aria-selected="true">
                          <span class="d-block d-sm-none"><i class="fas fa-home"></i></span>
                          <span class="d-none d-sm-block">FFMPeg</span>    
                        </a>
                      </li>
                      <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#profile" role="tab" aria-selected="false">
                          <span class="d-block d-sm-none"><i class="far fa-user"></i></span>
                          <span class="d-none d-sm-block">PHP</span>    
                        </a>
                      </li>
                    </ul>
                    <div class="tab-content p-3 text-muted">
                      <div class="tab-pane active" id="home" role="tabpanel">
                        <p class="mb-0">
                          <h4 class="card-title mb-4">FFMpeg Log <small style="color:gray"><?php
echo $LogName1;
?></small></h4>
                          <div class="table-responsive"><pre style="height:500px;overflow: auto;"><?php
echo $FFMpegLog;
?></pre></div>
                        </p>
                      </div>
                      <div class="tab-pane" id="profile" role="tabpanel">
                        <p class="mb-0">
                          <h4 class="card-title mb-4">PHP Log <small style="color:gray"><?php
echo $LogName2;
?></small></h4>
                          <div class="table-responsive"><pre style="height:500px;overflow: auto;"><?php
echo $PHPLog;
?></pre></div>
                        </p>
                      </div>
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
      function ReloadPage(){
        lines=$('#Lines').val();
        window.location.replace("?id=<?php
echo $ID;
?>&l="+lines);
      }
    </script>
  </body>
</html>