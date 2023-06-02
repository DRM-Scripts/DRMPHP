<?php
include "_config.php";
if(!$App->LoggedIn())header('location: login.php');
$Err1="";
$Err2="";
if(isset($_POST["Save"]) && $_POST["Save"]==1){
  $ChanID=$App->SaveChannel($_POST);
  $Data=$App->GetChannel($ChanID);
  $AudioIDs = explode(",", $Data["AudioID"]);
  if(isset($_POST["SaveRestart"]) && $_POST["SaveRestart"] == "Save & Restart"){
    $App->StopDownload(array("ChanID" => $ChanID));
    $App->StartDownload(array("ChanID" => $ChanID));
  }
}else{
  $ChanID = intval($_POST["ID"]);
  if($ChanID == 0){
    $ChanID = intval($_GET["id"]);
  }
  if($ChanID > 0){
    $Data=$App->GetChannel($ChanID);
    $AudioIDs = explode(",", $Data["AudioID"]);
  }else{
    $Data=$_POST;
    $AudioIDs = $_POST["AudioIDs"];
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
                                    <?php if($ChanID){ ?>
                                    Edit channel: <span class="bg-light"><?php
echo $Data["ChannelName"];
?></span>
                                    <?php }else{?>
                                    Add New channel
                                    <?php }?>
                                </h4>

                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item"><a href="javascript: void(0);">Main</a></li>
                                        <li class="breadcrumb-item active">
                                            <?php if($ChanID){ ?>
                                            Edit channel
                                            <?php }else{?>
                                            Add new channel
                                            <?php }?>
                                        </li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <a href="index.php" style="float:right;"
                                class="mb-3 btn btn-light waves-effect btn-label waves-light"><i
                                    class="bx bxs-left-arrow-circle label-icon"></i> Back to list</a>
                        </div>
                    </div>


                    <form method="POST">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="card">
                                    <div class="card-body">
                                        <h4 class="card-title">Channel information</h4>
                                        <div class="mb-3 row">
                                            <label class="col-md-2 col-form-label">Name</label>
                                            <div class="col-md-10">
                                                <input class="form-control" type="text" id="ChannelName"
                                                    name="ChannelName" value="<?php
echo $Data["ChannelName"];
?>">
                                            </div>
                                        </div>
                                        <div class="mb-3 row">
                                            <label class="col-md-2 col-form-label">Category</label>
                                            <div class="col-md-10">
                                                <select class="form-select" id="CatID" name="CatID">
                                                    <?php $Cats = $App->GetAllCats();
                          for($i=0;$i<count($Cats);$i++){
                            if($Cats[$i]["CatID"]==$Data["CatID"])$Selected="selected";else $Selected="";
                            ?>
                                                    <option <?php
echo $Selected?> value="<?=$Cats[$i]["CatID"];
?>">
                                                        <?php
echo $Cats[$i]["CatName"];
?></option>
                                                    <?
                          }
                          ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="mb-3 row">
                                            <label class="col-md-2 col-form-label">MPD</label>
                                            <div class="col-md-10">
                                                <input class="form-control" type="text" id="Manifest" name="Manifest"
                                                    value="<?php
echo $Data["Manifest"];
?>">
                                            </div>
                                        </div>
                                        <div class="mb-3 row">
                                            <label class="col-md-2 col-form-label">KID</label>
                                            <div class="col-md-10">
                                                <input class="form-control" type="text" id="KID[]" name="KID[]" value="<?php
echo $Data["Keys"][0]["KID"];
?>">
                                                <a href="javascript: void(0)" class="btn btn-primary btn-sm"
                                                    onclick="GetKID()">Get KID</a>
                                            </div>
                                        </div>
                                        <div class="mb-3 row">
                                            <label class="col-md-2 col-form-label">Key</label>
                                            <div class="col-md-10">
                                                <input class="form-control" type="text" id="Key[]" name="Key[]" value="<?php
echo $Data["Keys"][0]["Key"];
?>">
                                                <a href="javascript: void(0)" class="btn btn-primary btn-sm"
                                                    onclick="addKey()">Add Key</a>
                                            </div>
                                        </div>
                                        <div id="keys">
                                            <?php
                        if(count($Data["Keys"]) > 1) {
                          for($i=1;$i<count($Data["Keys"]);$i++){
                            ?>
                                            <div class="mb-3 row">
                                                <label class="col-md-2 col-form-label">KID</label>
                                                <div class="col-md-10">
                                                    <input class="form-control" type="text" id="KID[]" name="KID[]"
                                                        value="<?php
echo $Data["Keys"][$i]["KID"];
?>">
                                                </div>
                                            </div>
                                            <div class="mb-3 row">
                                                <label class="col-md-2 col-form-label">Key</label>
                                                <div class="col-md-10">
                                                    <input class="form-control" type="text" id="Key[]" name="Key[]"
                                                        value="<?php
echo $Data["Keys"][$i]["Key"];
?>">
                                                </div>
                                            </div>
                                            <?php
                          }
                        }
                        ?>
                                        </div>

                                        <h4 class="card-title mt-5">Downloading parameters</h4>

                                        <div class="mb-3 row">
                                            <label class="col-md-2 col-form-label">Joiner</label>
                                            <div class="col-md-10">
                                                <input class="form-control" type="text" id="SegmentJoiner"
                                                    name="SegmentJoiner" onchange="CalcTime()" onkeyup="CalcTime()"
                                                    value="<?php
echo $Data["SegmentJoiner"];
?>">
                                            </div>
                                        </div>
                                        <div class="mb-3 row">
                                            <label class="col-md-2 col-form-label">Playlist</label>
                                            <div class="col-md-10">
                                                <input class="form-control" type="text" id="PlaylistLimit"
                                                    name="PlaylistLimit" value="<?php
echo $Data["PlaylistLimit"];
?>">
                                            </div>
                                        </div>
                                        <div class="mb-3 row">
                                            <label class="col-md-2 col-form-label">Timeline</label>
                                            <div class="col-md-10">
                                                <input class="form-control" type="text" id="URLListLimit"
                                                    name="URLListLimit" value="<?php
echo $Data["URLListLimit"];
?>">
                                            </div>
                                        </div>

                                        <div class="mb-3 row">
                                            <label class="col-md-2 col-form-label">Useragent</label>
                                            <div class="col-md-10">
                                                <input class="form-control" type="text" id="DownloadUseragent"
                                                    name="DownloadUseragent" value="<?php
echo $Data["DownloadUseragent"];
?>">
                                            </div>
                                        </div>
                                        <div class="mb-3 row">
                                            <label class="col-md-2 col-form-label">Header (e.g : Authorization: Bearer
                                                xxxxx)</label>
                                            <div class="col-md-10">
                                                <input class="form-control" type="text" id="customHeaders[]"
                                                    name="customHeaders[]" value="<?php 
                            if(count($Data["CustomHeaders"]) > 0) {
                              echo $Data["CustomHeaders"][0]["Value"];
                            }
                          ?>">
                                                <a href="javascript: void(0)" class="btn btn-primary btn-sm"
                                                    onclick="addHeader()">Add Header</a>
                                            </div>
                                        </div>
                                        <div id="headers">
                                            <?php
                        if(count($Data["CustomHeaders"]) > 1) {
                          for($i=1;$i<count($Data["CustomHeaders"]);$i++){
                            ?>
                                            <div class="mb-3 row">
                                                <label class="col-md-2 col-form-label">Header</label>
                                                <div class="col-md-10">
                                                    <input class="form-control" type="text" id="customHeaders[]"
                                                        name="customHeaders[]" value="<?php 
                                echo $Data["CustomHeaders"][$i]["Value"]
                                ?>">
                                                </div>
                                            </div>
                                            <?php
                          }
                        }
                        ?>
                                        </div>
                                        <div class="mb-3 row">
                                            <label class="col-md-2 col-form-label">Allowed IP</label>
                                            <div class="col-md-10">
                                                <textarea class="form-control" id="AllowedIP" name="AllowedIP"><?php
echo $Data["AllowedIP"];
?></textarea>
                                            </div>
                                        </div>
                                        <div class="mb-3 row">
                                            <label class="col-md-2 col-form-label">Auto Restart</label>
                                            <div class="col-md-10">
                                                <Select id="AutoRestart" name="AutoRestart">
                                                    <?php if($Data["AutoRestart"]==1)$Selected="selected";else $Selected="";?>
                                                    <option <?php
echo $Selected;
?> value="1">Enabled</option>
                                                    <?php if($Data["AutoRestart"]==0)$Selected="selected";else $Selected="";?>
                                                    <option <?php
echo $Selected;
?> value="0">Disabled</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="mb-3 row">
                                            <label class="col-md-2 col-form-label"></label>
                                            <div class="col-md-10">
                                                <button type="submit"
                                                    class="btn btn-success waves-effect btn-label waves-light"><i
                                                        class="bx bxs-save label-icon"></i> Save</button>
                                                <input id="SaveRestart" name="SaveRestart" type="submit"
                                                    class="btn btn-secondary waves-effect waves-light"
                                                    value="Save & Restart">
                                                <a href="index.php"
                                                    class="btn btn-light waves-effect btn-label waves-light"><i
                                                        class="bx bx-undo label-icon"></i> Cancel</a>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="card">
                                    <div class="card-body">
                                        <a href="javascript: void(0)" style="float:right" onclick="TestChan()"
                                            class="btn btn-outline-warning waves-effect btn-label waves-light"><i
                                                class="bx bx-crosshair label-icon"></i> Parse MPD</a>
                                        <h4 class="card-title">Test MPD Parsing</h4>
                                        <br>
                                        <div class="mb-3 row">
                                            <label class="col-md-2 col-form-label">Result</label>
                                            <div class="col-md-10" id="TestResult">
                                            </div>
                                        </div>
                                        <div class="mb-3 row">
                                            <label class="col-md-2 col-form-label">Delay</label>
                                            <div class="col-md-10" id="TestResult">
                                                <div id="Delay" style="font-family: monospace;color:orange">[Parse MPD]
                                                    to get latest values. Also check [Joiner] value</div>
                                            </div>
                                        </div>
                                        <div class="mb-3 row">
                                            <label class="col-md-2 col-form-label">Audio</label>
                                            <div class="col-md-10" id="AudioIDs">
                                                <?php 
                        $Variants = $App->GetAudioIDs($Data["ID"]);
                        for($i=0;$i<count($Variants);$i++){
                          if(in_array($Variants[$i]["AudioID"], $AudioIDs)) $Checked="checked";else $Checked="";
                          ?>
                                                <label for="AudioID_<?php
echo $Variants[$i]["AudioID"];
?>">
                                                    <input <?php
echo $Checked;
?> type="checkbox" name="AudioIDs[]" id="AudioID_<?php
echo $Variants[$i]["AudioID"];
?>" value="<?php
echo $Variants[$i]["AudioID"];
?>">
                                                    <?php
echo $Variants[$i]["AudioID"];
?>
                                                </label><br>
                                                <?php
                        }
                        ?>
                                                <!--
                          <select class="form-select" id="AudioID" name="AudioID">
                            <option value="">-- Please select --</option>
                            <?php
                            if($Data["AudioIDs"]){
                              for($i=0;$i<count($Data["AudioIDs"]);$i++){
                                if($Data["AudioID"] == $Data["AudioIDs"][$i][0])$Selected="selected";else $Selected="";
                            ?>
                                <option <?php
echo $Selected?> value="<?=$Data["AudioIDs"][$i][0]?>"><?=$Data["AudioIDs"][$i][0];
?></option>
                            <?php
                              }
                            }
                            ?>
                          </select>
                          -->
                                            </div>
                                        </div>
                                        <div class="mb-3 row">
                                            <label class="col-md-2 col-form-label">Video</label>
                                            <div class="col-md-10">
                                                <select class="form-select" id="VideoID" name="VideoID">
                                                    <option value="">-- Please select --</option>
                                                    <?php
                            if($Data["VideoIDs"]){
                              for($i=0;$i<count($Data["VideoIDs"]);$i++){
                                if($Data["VideoID"] == $Data["VideoIDs"][$i][0])$Selected="selected";else $Selected="";
                            ?>
                                                    <option <?php
echo $Selected?> value="<?=$Data["VideoIDs"][$i][0];
?>">
                                                        <?php
echo $Data["VideoIDs"][$i][0];
?></option>
                                                    <?php
                              }
                            }
                            ?>
                                                </select>
                                            </div>
                                        </div>
                                        <br />
                                        <h4 class="card-title mt-5">Proxy Settings</h4>
                                        <br>
                                        <div class="mb-3 row">
                                            <label class="col-md-2 col-form-label">Use Proxy</label>
                                            <div class="col-md-10">
                                               <input type="checkbox" id="UseProxy" name="UseProxy" value="1" <?php
if($Data["UseProxy"]==1)echo "checked";
?>/>
                                            </div>
                                        </div>
                                        <div class="mb-3 row">
                                            <label class="col-md-2 col-form-label">Proxy Host</label>
                                            <div class="col-md-10">
                                                <input class="form-control" type="text" id="ProxyURL" name="ProxyURL"
                                                    value="<?php
echo $Data["ProxyURL"];
?>">
                                            </div>
                                        </div>
                                        <div class="mb-3 row">
                                            <label class="col-md-2 col-form-label">Proxy Port</label>
                                            <div class="col-md-10">
                                                <input class="form-control" type="text" id="ProxyPort" name="ProxyPort"
                                                    value="<?php
echo $Data["ProxyPort"];
?>">
                                            </div>
                                        </div>

                                        <div class="mb-3 row">
                                            <label class="col-md-2 col-form-label">Proxy User</label>
                                            <div class="col-md-10">
                                                <input class="form-control" type="text" id="ProxyUser" name="ProxyUser"
                                                    value="<?php
echo $Data["ProxyUser"];
?>">
                                            </div>
                                        </div>

                                        <div class="mb-3 row">
                                            <label class="col-md-2 col-form-label">Proxy Password</label>
                                            <div class="col-md-10">
                                                <input class="form-control" type="text" id="ProxyPass"
                                                    name="ProxyPass" value="<?php
echo $Data["ProxyPass"];
?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="Dur" id="Dur" value="0">
                        <input type="hidden" name="Update" id="Update" value="0">
                        <input type="hidden" name="Save" value="1">
                        <input type="hidden" name="ID" value="<?php
echo $ChanID;
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

    <script>
    function TestChan() {
        var mpd = $('#Manifest').val();
        var useproxy = $('#UseProxy').is(":checked");
        var proxyurl = $('#ProxyURL').val();
        var proxyport = $('#ProxyPort').val();
        var proxyuser = $('#ProxyUser').val();
        var proxypass = $('#ProxyPass').val();
        var useragent = $('#Useragent').val();

        $('#TestResult').html('<img src="assets/images/loader.gif" style="display:block">');

        $.post("_func.php", {
                Func: "TestMPD",
                MPD: mpd,
                UseProxy: useproxy,
                ProxyURL: proxyurl,
                ProxyPort: proxyport,
                ProxyUser: proxyuser,
                ProxyPass: proxypass,
                Useragent: useragent
            })
            .done(function(data) {
                if (data) {
                    res = JSON.parse(data);
                    $('#TestResult').html('<pre>' + res["str"] + '</pre>');
                    var x = res["str"].split(/\r?\n/);
                    var dur = parseFloat(x[x.length - 2].split(":")[1].replace("/Seconds", ""));
                    var update = parseFloat(x[x.length - 1].split(":")[1].replace("/Seconds", ""));
                    $('#Dur').val(dur);
                    $('#Update').val(update);
                    CalcTime();

                    //var oldaid =$('#AudioID').val();
                    var oldvid = $('#VideoID').val();

                    var op = '';
                    var a = res["a"];
                    for (i = 0; i < a.length; i++) {
                        //if(a[i] == oldaid)checked='checked';else checked='';
                        op += '<label for="AudioID_' + a[i] +
                            '"><input type="checkbox" name="AudioIDs[]" id="AudioID_' + a[i] + '" value="' + a[i] +
                            '"> ' + a[i] + '</label><br>';
                    }
                    $('#AudioIDs').html(op);

                    op = '<option value="">--Please select --</option>';
                    var v = res["v"];
                    for (i = 0; i < v.length; i++) {
                        if (v[i] == oldvid) selected = 'selected';
                        else selected = '';
                        op += '<option ' + selected + ' value="' + v[i] + '">' + v[i] + '</option>'
                    }
                    $('#VideoID').html(op);
                }
            })
    }

    function CalcTime() {
        var joiner = parseInt($('#SegmentJoiner').val());
        if (isNaN(joiner)) joiner = 0;
        var duration = $('#Dur').val();
        var update = $('#Update').val();
        var playlistitems = 2;
        var delay = (joiner * duration) + (joiner * update) + (duration * playlistitems);
        if (delay == 0) {
            $('#Delay').html('[Parse MPD] to get latest values. Also check [Joiner] value')
        } else {
            $('#Delay').html('aproximately = ' + (delay + 2) + ' Seconds')
        }
    }

    function GetKID() {
        var manifest = $('#Manifest').val();
        var useproxy = $('#UseProxy').is(":checked");
        var proxyurl = $('#ProxyURL').val();
        var proxyport = $('#ProxyPort').val();
        var proxyuser = $('#ProxyUser').val();
        var proxypass = $('#ProxyPass').val();
        var useragent = $('#Useragent').val();
        $.post("_func.php", {
                Func: "GetKID",
                URL: manifest,
                UseProxy: useproxy,
                ProxyURL: proxyurl,
                ProxyPort: proxyport,
                ProxyUser: proxyuser,
                ProxyPass: proxypass,
                Useragent: useragent
            })
            .done(function(data) {
                let res = JSON.parse(data);
                clearKey();
                $('#KID\\[\\]').val(res[0]);
                if (res.length > 1) {
                    for (i = 1; i < res.length; i++) {
                        addKey();
                        $('#KID\\[\\]').eq(i).val(res[i]);
                    }
                }
            })
    }

    function clearKey() {
        $('#keys').html('');
    }

    function addKey() {
        var childCount = $('#keys').children().length;
        if (childCount >= 6) {
            alert('Maximum 4 keys allowed');
            return;
        }
        $target = $('#keys');
        $target.append(
            '<div class="mb-3 row"><label class="col-md-2 col-form-label">KID</label><div class="col-md-10"><input class="form-control" type="text" id="KID[]" name="KID[]" value="" placeholder="KID"></div></div><div class="mb-3 row"><label class="col-md-2 col-form-label">Key</label><div class="col-md-10"><input type="text" class="form-control" name="Key[]" placeholder="KEY"></div></div>'
        );
    }

    function addHeader() {
        var childCount = $('#headers').children().length;
        if (childCount >= 6) {
            alert('Maximum headers reached');
            return;
        }
        $target = $('#headers');
        $target.append(
            '<div class="mb-3 row"><label class="col-md-2 col-form-label">Header</label><div class="col-md-10"><input class="form-control" type="text" id="customHeaders[]" name="customHeaders[]" value="" placeholder="Header"></div></div>'
        );
    }
    </script>
</body>

</html>
