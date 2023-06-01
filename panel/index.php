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
                  <h4 class="mb-sm-0 font-size-18">Channels</h4>

                  <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                      <li class="breadcrumb-item"><a href="javascript: void(0);">Main</a></li>
                      <li class="breadcrumb-item active">Channels List</li>
                    </ol>
                  </div>

                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-12">
                <a href="add.php" style="float:right" class="mb-3 btn btn-light waves-effect btn-label waves-light"><i class="bx bx-list-plus label-icon"></i> Add New</a>
                <a href="javascript: void(0)" onclick="All('Start')" class="mb-3 btn btn-success waves-effect btn-label waves-light"><i class="bx bx-play label-icon"></i> Start All</a>
                <a href="javascript: void(0)" onclick="All('Stop')" class="mb-3 btn btn-warning waves-effect btn-label waves-light"><i class="bx bx-stop label-icon"></i> Stop All</a>
                <a href="javascript: void(0)" onclick="ExportAll('api')" class="mb-3 btn btn-info waves-effect btn-label waves-light"><i class="bx bxs-download label-icon"></i> Download .m3u (api)</a>
                <a href="javascript: void(0)" onclick="ExportAll('hls')" class="mb-3 btn btn-info waves-effect btn-label waves-light"><i class="bx bxs-download label-icon"></i> Download .m3u (hls)</a>
                <img src="assets/images/loader.gif" id="Loader" style="display:none;vertical-align: middle;width:48px;height:48px;margin-top: -12px;">
              </div>
            </div>
            <div class="row">
              <div class="col-12">
                <form method="post">
                  <div class="row">
                    <div class="col-lg-3">
                      <div class="mb-3">
                        <label class="form-label">Channel Name</label>
                        <input type="text" class="form-control" id="SearchChanName" name="SearchChanName" placeholder="Enter channel name" value="<?php
echo $_POST["SearchChanName"];
?>">
                      </div>
                    </div>
                    <div class="col-lg-3">
                      <div class="mb-3">
                        <label class="form-label">MPD Url</label>
                        <input type="text" class="form-control" id="SearchMPDUrl" name="SearchMPDUrl" placeholder="Enter manifest url" value="<?php
echo $_POST["SearchMPDUrl"];
?>">
                      </div>
                    </div>
                    <div class="col-lg-3">
                      <div class="mb-3">
                        <label class="form-label">Category</label>
                        <input type="text" class="form-control" id="SearchCatName" name="SearchCatName" placeholder="Enter category" value="<?php
echo $_POST["SearchCatName"];
?>">
                      </div>
                    </div>
                    <div class="col-lg-3">
                      <div class="mb-3">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" name="Search" id="Search" class="btn btn-light waves-effect " style="width:100%">Search</button>
                        <input type="hidden" name="search" value="1">
                      </div>
                    </div>
                  </div>
                </form>
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
                            <th class="align-middle">Uptime</th>
                            <th class="align-middle">Resolution</th>
                            <th class="align-middle">Bitrate</th>
                            <th class="align-middle">FPS</th>
                            <th class="align-middle">Codecs</th>
                            <th class="align-middle">PID 1</th>
                            <th class="align-middle">PID 2</th>
                            <th class="align-middle">Action</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php
                          $Data=$App->GetAllChannels($_POST);
                          if($Data){
                            for($i=0;$i<count($Data);$i++){
                              $Chan=$Data[$i];
                              $info = json_decode($Chan["info"], true);
                          ?>
                              <tr>
                                <td id="status_<?php
echo $Chan["ID"];
?>" style="text-align: center;">
                                  <?php
echo $Chan["ID"];
?><br>
                                  <?php if($Chan["Status"]=="Downloading" && (!file_exists("/proc/".$Chan["PID"]) ||!file_exists("/proc/".$Chan["FPID"]))){?>
                                    <span class="badge badge-pill badge-soft-danger">Error</span>
                                  <?php } else {?>
                                    <?php if($Chan["Status"]=="KeyError") {?>
                                      <span class="badge badge-pill badge-soft-danger">Key Error</span>
                                    <?php } else {?>
                                    <?php if($Chan["Status"]=="Stopped") { ?>
                                      <span class="badge badge-pill badge-soft-dark">Offline</span>
                                    <?php } elseif($Chan["Status"]=="Downloading") { ?>
                                      <span class="badge badge-pill badge-soft-success">Online</span>
                                    <?php } elseif($Chan["Status"]=="Not Supported") { ?>
                                      <span class="badge badge-pill badge-soft-danger">Not supported</span>
                                    <?php } elseif($Chan["Status"]=="Offline") { ?>
                                      <span class="badge badge-pill badge-soft-warning">O</span>
                                  <?php }
                                    }
                                  }?>
                                </td>
                                <td style="width:450px;overflow-wrap: break-word;word-break: break-all;">
                                  <?php
                                  $Url="http".(!empty($_SERVER['HTTPS'])?"s":"")."://".$_SERVER['SERVER_NAME'].":".$_SERVER['SERVER_PORT']."/".dirname($_SERVER['SCRIPT_NAME'])."/api.php?action=m3u8&id=".str_replace(" ", "_", $Chan["ChannelName"]);
                                  ?>
                                  <a class="btn btn-sm btn-default" target="_blank" href='<?php
echo $Url;
?>'><span class="bx bxs-playlist"></span></a>
                                  <a href="add.php?id=<?php
echo $Chan["ID"]?>" class="text-body fw-bold"><?=$Chan["ChannelName"]?></a> <small style="float:right"><span class="badge badge-soft-pink">Category</span> <?=$Chan["CatName"];
?></small><br>
                                  <small style="margin-left: 32px;"><span class="badge badge-soft-info">Audio</span> <?php
echo $Chan["AudioID"]?> <span class="badge badge-soft-info">Video</span> <?=$Chan["VideoID"];
?></small>
                                </td>
                                <td><span id="uptime_<?php
echo $Chan["ID"]?>"><?=$Chan["Uptime"];
?></span></td>
                                <td><span id="res_<?php
echo $Chan["ID"]?>"><?=($info["width"]."x".$info["height"]);
?></span></td>
                                <td><span id="bitrate_<?php
echo $Chan["ID"]?>"><?=round($info["bitrate"]/1000, 1)."kb";
?></span></td>
                                <td><span id="fps_<?php
echo $Chan["ID"]?>"><?=str_replace("/1", "", $info["framerate"]);
?></span></td>
                                <td><span id="codecs_<?php
echo $Chan["ID"]?>"><?=($info["vcodec"]."/".$info["acodec"]);
?></span></td>
                                <td><span id="pid_<?php
echo $Chan["ID"]?>"><?=$Chan["PID"];
?></span></td>
                                <td><span id="fpid_<?php
echo $Chan["ID"]?>"><?=$Chan["FPID"];
?></span></td>
                                <td>
                                  <div class="btn-group btn-group-sm">
                                    <a class="btn btn-outline-dark" title="hhh" href="add.php?id=<?php
echo $Chan["ID"];
?>"><i class="bx bxs-edit-alt"></i></a>
                                    <a class="btn btn-outline-info" href="javascript: void(0)" onclick="ShowConfig('<?php
echo $Chan["ID"];
?>')"><i class="bx bxs-cog"></i></a>
                                    <?php if($Chan["Status"]=="Stopped"){ ?>
                                      <a href="javascript: void(0)" class="btn btn-outline-success" onclick="Download('<?php
echo $Chan["ID"]?>', '<?=$Chan["Manifest"]?>', '<?=$Chan["AudioID"]?>', '<?=$Chan["VideoID"];
?>')"><i class="bx bxs-download"></i></a>
                                    <?php } else { ?>
                                      <a class="btn btn-outline-warning" href="javascript: void(0)" onclick="StopDownload('<?php
echo $Chan["ID"];
?>')"><i class="bx bx-stop"></i></a>
                                    <?php }?>
                                  </div>

                                  <a class="btn btn-light btn-sm" href="log.php?id=<?php
echo $Chan["ID"];
?>&l=20"><i class="bx bx-clipboard"></i></a>
                                  <a class="btn btn-danger btn-sm" href="javascript: void(0)" onclick="DeleteChannel('<?php
echo $Chan["ID"];
?>')"><i class="bx bx-trash"></i></a>
                                </td>
                              </tr>
                              <tr>
                                <td colspan="7" id="Config_<?php
echo $Chan["ID"];
?>" style="display:none;padding-left:100px"></td>
                              </tr>
                            <?}
                          } else {
                            ?>
                            <tr>
                              <td colspan="7" class="text-center">
                                <strong>No Channels Found</strong>
                              </td>
                            </tr>
                          <?php
                          }
                          ?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div> <!-- container-fluid -->
        <?php 
        include "_footer.php";
        ?>
      </div><!-- content -->
    </div><!-- container -->
    <?php 
    include "_rightbar.php";
    ?>

    <div class="rightbar-overlay"></div>

    <script src="assets/libs/jquery/jquery.min.js"></script>
    <script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/libs/metismenu/metisMenu.min.js"></script>
    <script src="assets/libs/simplebar/simplebar.min.js"></script>
    <script src="assets/libs/node-waves/waves.min.js"></script>
    <script src="assets/js/app.js"></script>
    <script>
      function ShowConfig(chanid){
        $('div[id^="Config_"]').empty();
        $('div[id^="Config_"]').hide();
        $('#Config_'+chanid).load("_chanconfig.php",{ChanID:chanid});
        $('#Config_'+chanid).show();
      } 

      function Download(chanid, manifest, audioid, videoid){
        $.post("_func.php",{Func:"StartDownload",ChanID:chanid, AudioID:audioid, VideoID:videoid, Manifest:manifest})
        .done(function(){
          window.location.reload();
        });
      }

      function StopDownload(chanid){
        $.post("_func.php",{Func:"StopDownload",ChanID:chanid})
        .done(function(){
          window.location.reload();
        })
      }
      function ExportAll(type){
        $.post("_func.php", {Func:"ExportAll", Type:type})
        .done(function(){
          window.open('export.php', '_blank').focus();
        })
      }
      function DeleteChannel(id){
        if (confirm("Are you sure to delete this channel?") == true) {
          $.post("_func.php", {Func:"DeleteChannel", ID:id})
          .done(function(){
            window.location.reload();
          })
        }
      }


      function All(action){
        $('#Loader').show();
        $.post("_func.php", {Func:"All", Action:action})
        .done(function(){
          window.location.reload();
        })
      }
      $(document).ready(function(){
        setInterval(UpdateTable, 5000);
      });
      function UpdateTable() {
        $.post("_func.php",{Func:"UpdateTable"})
        .done(function(res){
          data=JSON.parse(res);
          for (var i = 0; i < data.length; i++) {    
            var id=data[i]["id"];
            $('#uptime_'+id).html(data[i]["uptime"]);
            $('#res_'+id).html(data[i]["res"]);
            $('#bitrate_'+id).html(data[i]["bitrate"]);
            $('#framerate_'+id).html(data[i]["framerate"]);
            $('#codecs_'+id).html(data[i]["codecs"]);
            $('#pid_'+id).html(data[i]["pid"]);
            $('#fpid_'+id).html(data[i]["fpid"]);
            if(data[i]["status"]=="KeyError"){
              $('#status_'+id).html(id+'<br><span class="badge badge-pill badge-soft-danger">Key Error</span>');
            }else{
              if(data[i]["status"]=="Downloading" && (data[i]["fpidexist"] == 0 || data[i]["pidexist"] == 0)){
                $('#status_'+id).html(id+'<br><span class="badge badge-pill badge-soft-danger">Error</span>');
              }else {
                if(data[i]["status"]=="Stopped"){ 
                  $('#status_'+id).html(id+'<br><span class="badge badge-pill badge-soft-dark">Offline</span>');
                }else {
                  if(data[i]["status"]=="Downloading"){ 
                    $('#status_'+id).html(id+'<br><span class="badge badge-pill badge-soft-success">Online</span>');
                  }else {
                    if(data[i]["status"]=="Not Supported"){
                      $('#status_'+id).html(id+'<br><span class="badge badge-pill badge-soft-danger">Not supported</span>');
                    }
                  }
                }
              }
          }}
        })
      }
    </script>
  </body>
</html>
