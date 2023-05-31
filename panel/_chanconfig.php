<?php
include "_config.php";
$ID = $_POST["ChanID"];
$Chan = $App->GetChannel($ID);
$Variants = $App->GetVariants($ID);
if ($Chan["Status"] == "Download") {
    $Display1 = "none";
    $Disabled1 = "disabled";
    $Display2 = "none";
    $Disabled2 = "disabled";
    $Msg = "Channel is downloading. stop the channel to make changes.";
} elseif ($Chan["Status"] == "Not Supported") {
    $Display1 = "";
    $Disabled1 = "";
    $Display2 = "";
    $Disabled2 = "";
    $Msg = "Channel manifest is not supported.";
} elseif ($Chan["Status"] == "Offline") {
    $Display1 = "none";
    $Disabled1 = "disabled";
    $Display2 = "";
    $Disabled2 = "";
    $Msg = "Channel offline.";
} elseif ($Chan["Status"] == "Error") {
    $Display1 = "";
    $Disabled1 = "";
    $Display2 = "";
    $Disabled2 = "";
    $Msg = "Error reading manifest.";
} else {
    $Display1 = "";
    $Disabled1 = "";
    $Display2 = "";
    $Disabled2 = "";
    $Msg = "";
}
?>
Select the varient you want to download:
<div class="input-append">
    <select <?php
echo $Disabled;
?> id="Variant">
        <option value="0">-- Select variant --</option>
        <?
    for($i=0;$i<count($Variants);$i++){
      $V=$Variants[$i];
      $AudioID=$V["AudioID"];
      $VideoID=$V["VideoID"];
      if($AudioID == $Chan["AudioID"] && $VideoID == $Chan["VideoID"])$Selected="Selected";else $Selected="";
    ?>
        <option <?php
echo $Selected;
?> value="<?php
echo ($AudioID . "|" . $VideoID);
?>">L: <?php
echo $V["Language"];
?>, A: <?=$V["AudioID"] . " " . $V["AudioBandwidth"];
?>, V: <?=$V["VideoID"] . " " . $V["VideoBandwidth"];
?></option>
        <?
    }
    ?>
    </select>
    <a <?php
echo $Disabled1;
?> style="display:<?=$Display1;
?>" class="btn btn-success btn-sm " href="javascript: void(0)" onclick="save()">save</a>
    <a <?php
echo $Disabled2;
?> style="display:<?php
echo $Display2;
?>" class="btn btn-warning btn-sm " href="javascript: void(0)" onclick="Parse()">Parse</a>
    <a class="btn btn-light btn-sm " href="javascript: void(0)" onclick="Cancel()">X</a>
</div>
<?php
if ($Msg) {
    ?>
<div class="alert alert-danger"><?php
echo $Msg;
    ?></div>
<?php
}
?>
<script>
function Parse() {
    var chanid = '<?php
echo $ID;
?>';
    $.post("_func.php", {
            Func: "Parse",
            ID: chanid
        })
        .done(function(data) {
            $('#Config_' + chanid).load("_chanconfig.php", {
                ChanID: chanid
            });
        });
}

function save() {
    $.post("_func.php", {
            Func: "SaveVariant",
            Variant: $('#Variant').val(),
            ChanID: '<?php
echo $ID;
?>'
        })
        .done(function() {
            $('div[id^="Config_"]').empty();
            $('div[id^="Config_"]').hide();
            window.location.reload();
        })
}

function Cancel() {
    $('td[id^="Config_"]').empty();
    $('td[id^="Config_"]').hide();
}
</script>