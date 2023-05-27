<?php
include "_config.php";
if(!$App->LoggedIn())header('location: login.php');
if(isset($_POST["DownloadPath"]) && $_POST["DownloadPath"] <> ""){
  $App->SaveSettings($_POST);
}
$active1="active";
$active2="";
$selected1="true";
$selected2="false";
$Msg="";

if($_GET["r"]==1 ){
  $Msg1 = "Backup restored.";
  $active1="";
  $active2="active";
  $selected1="false";
  $selected2="true";
}
if($_GET["d"]==1 ){
  $Msg2 = "Backup deleted.";
  $active1="";
  $active2="active";
  $selected1="false";
  $selected2="true";
}
if((isset($_POST["Restore"]) && $_POST["Restore"] ==1) || $_GET["d"]==1 || $_GET["r"]==1){
  $App->RestoreDatabase($_FILES);
  $active1="";
  $active2="active";
  $selected1="false";
  $selected2="true";
}
if(isset($_POST["Backup"]) && $_POST["Backup"] ==1){
  $BackupRes=$App->BackupDatabase();
  $active1="";
  $active2="active";
  $selected1="false";
  $selected2="true";
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
                  <h4 class="mb-sm-0 font-size-18">Settings</h4>

                  <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                      <li class="breadcrumb-item"><a href="javascript: void(0);">Main</a></li>
                      <li class="breadcrumb-item active">Settings</li>
                    </ol>
                  </div>

                </div>
              </div>
            </div>


            <div class="row">
              <div class="col-lg-12">
                <div class="card-body">
                  <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item">
                      <a class="nav-link <?php
echo $active1?>" data-bs-toggle="tab" href="#t1" role="tab" aria-selected="<?=$selected1;
?>">
                        <span class="d-block d-sm-none"><i class="fas fa-home"></i></span>
                        <span class="d-none d-sm-block">General</span>    
                      </a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link <?php
echo $active2?>" data-bs-toggle="tab" href="#t2" role="tab" aria-selected="<?=$selected2;
?>">
                        <span class="d-block d-sm-none"><i class="far fa-user"></i></span>
                        <span class="d-none d-sm-block">Backup</span>    
                      </a>
                    </li>
                  </ul>
                  <div class="tab-content p-3">
                    <div class="tab-pane <?php
echo $active1;
?>" id="t1" role="tabpanel">
                      <div class="mb-0">
                        <h4 class="card-title">Global Settings</h4>
                        <p class="card-title-desc">This settings affects all channels. Global settings is used when individual channels settings not set.</p>
                        <form method="POST">
                          <div class="mb-3 row">
                            <label class="col-md-2 col-form-label">Segment Download Path</label>
                            <div class="col-md-10">
                              <input class="form-control" type="text" id="DownloadPath" name="DownloadPath" value="<?php
echo $App->GetConfig("DownloadPath");
?>">
                            </div>
                          </div>
                          <div class="mb-3 row">
                            <label class="col-md-2 col-form-label">Downloader Path</label>
                            <div class="col-md-10">
                              <input class="form-control" type="text" id="DownloaderPath" name="DownloaderPath" value="<?php
echo $App->GetConfig("DownloaderPath");
?>">
                            </div>
                          </div>
                          <div class="mb-3 row">
                            <label class="col-md-2 col-form-label">DB Backup Path</label>
                            <div class="col-md-10">
                              <input class="form-control" type="text" id="BackupPath" name="BackupPath" value="<?php
echo $App->GetConfig("BackupPath");
?>">
                            </div>
                          </div>
                          <div class="mb-3 row">
                            <label class="col-md-2 col-form-label">Max Log Size (bytes)</label>
                            <div class="col-md-10">
                              <input class="form-control" type="text" id="MaxLogSize" name="MaxLogSize" value="<?php
echo $App->GetConfig("MaxLogSize");
?>">
                            </div>
                          </div>
                          <div class="mb-3 row">
                            <label class="col-md-2 col-form-label">Mp4Decrypt Path</label>
                            <div class="col-md-10">
                              <input class="form-control" type="text" id="BinPath" name="BinPath" value="<?php
echo $App->GetConfig("BinPath");
?>">
                            </div>
                          </div>
                          <div class="mb-3 row">
                            <label class="col-md-2 col-form-label">M3U8 Download URL</label>
                            <div class="col-md-10">
                              <input class="form-control" type="text" id="M3UDownloadURL" name="M3UDownloadURL" value="<?php
echo $App->GetConfig("M3UDownloadURL");
?>">
                            </div>
                          </div>
                          <div class="mb-3 row">
                            <label class="col-md-2 col-form-label">Segment Joiner Count</label>
                            <div class="col-md-10">
                              <input class="form-control" type="text" id="SegmentJoiner" name="SegmentJoiner" value="<?php
echo $App->GetConfig("SegmentJoiner");
?>">
                            </div>
                          </div>
                          <div class="mb-3 row">
                            <label class="col-md-2 col-form-label">Default Playlist Items</label>
                            <div class="col-md-10">
                              <input class="form-control" type="text" id="PlaylistLimit" name="PlaylistLimit" value="<?php
echo $App->GetConfig("PlaylistLimit");
?>">
                            </div>
                          </div>
                          <div class="mb-3 row">
                            <label class="col-md-2 col-form-label">Default Timeline List Limit</label>
                            <div class="col-md-10">
                              <input class="form-control" type="text" id="URLListLimit" name="URLListLimit" value="<?php
echo $App->GetConfig("URLListLimit");
?>">
                            </div>
                          </div>
                          <div class="mb-3 row">
                            <label class="col-md-2 col-form-label">FFMpeg Merge Command</label>
                            <div class="col-md-10">
                              <input class="form-control" type="text" id="FFMpegCMD" name="FFMpegCMD" value="<?php
echo $App->GetConfig("FFMpegCMD");
?>">
                            </div>
                          </div>
                          <div class="mb-3 row">
                            <label class="col-md-2 col-form-label">Delete Encrypted</label>
                            <div class="col-md-10">
                              <input class="form-control" type="text" id="DeleteEncryptedAfterDecrypt" name="DeleteEncryptedAfterDecrypt" value="<?php
echo $App->GetConfig("DeleteEncryptedAfterDecrypt");
?>">
                            </div>
                          </div>
                          <div class="mb-3 row">
                            <label class="col-md-2 col-form-label">Delete Decrypted</label>
                            <div class="col-md-10">
                              <input class="form-control" type="text" id="DeleteDecryptedAfterMerge" name="DeleteDecryptedAfterMerge" value="<?php
echo $App->GetConfig("DeleteDecryptedAfterMerge");
?>">
                            </div>
                          </div>
                          <div class="mb-3 row">
                            <label class="col-md-2 col-form-label">Download Useragent</label>
                            <div class="col-md-10">
                              <input class="form-control" type="text" id="DownloadUseragent" name="DownloadUseragent" value="<?php
echo $App->GetConfig("DownloadUseragent");
?>">
                            </div>
                          </div>
                          <div class="mb-3 row">
                            <label class="col-md-2 col-form-label">Auto restart channels</label>
                            <div class="col-md-10">
                              <input class="form-control" type="text" id="AutoRestart" name="AutoRestart" value="<?php
echo $App->GetConfig("AutoRestart");
?>">
                            </div>
                          </div>
                          <div class="mb-3 row">
                            <label class="col-md-2 col-form-label"></label>
                            <div class="col-md-10">
                              <button type="submit" class="btn btn-success waves-effect btn-label waves-light"><i class="bx bxs-save label-icon"></i> Save</button>
                              <a href="index.php" class="btn btn-light waves-effect btn-label waves-light"><i class="bx bx-undo label-icon"></i> Cancel</a>
                            </div>
                          </div>
                        </form>
                      </div>
                    </div>
                    <div class="tab-pane <?php
echo $active2;
?>" id="t2" role="tabpanel">
                      <div class="mb-0">
                        <h4 class="card-title mb-3">Database Backup</h4>
                        <h3 class="card-title">Create backup</h3>
                        <p>Create backup from current database. This backup does not inclide downloading logs.</p>
                        <?php
                        if($BackupRes[0]=="Error"){
                          ?>
                          <pre style="color:red">Error creating backup. Please check system logs.<br><b>Error: <?php
echo $BakcupRes[1];
?></b></pre>
                          <?php
                        }else{
                          if(isset($BackupRes[0])){
                            file_put_contents("getbkup.txt", 1);
                          ?>
                          <pre style="color:lime">Backup created at: <?php
echo $BackupRes[0]?><br>Filename: <?=$BackupRes[1]?></pre>Click to <a target="_blank" href="getbkup.php?file=<?=$BackupRes[1];
?>">Download</a><br><br>
                          <?php
                          }
                        }
                        ?>
                        <form method="POST">
                          <button type="submit" class="btn btn-outline-info waves-effect btn-label waves-light" type="button" id="Backup"><i class="bx bx-list-plus label-icon"></i> Create Backup</button>
                          <input type="hidden" name="Backup" value="1">
                        </form>
                        <hr class="m-5">
                        <h2 class="card-title">Restore system backup</h2>
                        <p>Restore database backup. <span class="badge badge-soft-danger font-size-14">This will override current database and can not be undone</span>.</p>
                        <?php if($Msg1){ ?>
                          <pre style="color:lime;margin-top: 10px;font-size: 14px;">Backup restored.</pre>
                          <?php }?>
                        <?php if($Msg2){ ?>
                          <pre style="color:red;margin-top: 10px;font-size: 14px;">Backup deleted.</pre>
                          <?php }?>
                        <?php
                        $Backups = $App->GetBackups();
                        ?>
                        <table class="table table-sm m-0" style="width:100%">
                        <?php for($i=0;$i<count($Backups);$i++){?>
                          <tr>
                            <td ><?php
echo $Backups[$i];
?></td>
                            <td style="width:400px">
                            <button type="button" onclick="RestoreBackup('<?php
echo $Backups[$i];
?>')" class="btn btn-outline-success btn-label waves-light" type="button" ><i class="bx bx-reset label-icon"></i> Restore </button>
                            <button type="button" onclick="DownloadBackup('<?php
echo $Backups[$i];
?>')" class="btn btn-outline-info btn-label waves-light" type="button" ><i class="bx bx-download label-icon"></i> Download</button>
                            <button type="button" onclick="DeleteBackup('<?php
echo $Backups[$i];
?>')" class="btn btn-outline-danger btn-label waves-light" type="button" ><i class="bx bx-trash label-icon"></i> Delete</button>                            
                            </td>                            
                          </tr>
                          <?php }?>
                        </table>

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
      function RestoreBackup(file){
        $.post("_func.php",{Func:"RestoreBackup", File:file})
        .done(function(){
          window.location.replace("?r=1");
        })
      }
      function DownloadBackup(file){
        $.post("_func.php",{Func:"DownloadBackup", File:file})
        .done(function(){
          window.open('getbkup.php?file='+file, '_blank').focus();
        })
      }
      function DeleteBackup(file){
        $.post("_func.php",{Func:"DeleteBackup", File:file})
        .done(function(){
          window.location.replace("?d=1");
        })
      }
    </script>
  </body>
</html>