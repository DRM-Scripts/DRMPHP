<?php
include "_config.php";
$AutoRestart=$App->GetConfig("AutoRestart");
if($AutoRestart){
  $WorkPath = $App->GetConfig("DownloadPath");
  $sql="select ID, ChannelName, Status, PID, FPID from channels where AutoRestart=1";
  $st=$App->DB->prepare($sql);
  $st->execute();
  $Data=$st->fetchAll();
  for($i=0;$i<count($Data);$i++){
    $PID = $Data[$i]["PID"];
    $FPID = $Data[$i]["FPID"];
    $ChanID = $Data[$i]["ID"];
    $ChName = str_replace(" ", "_", $Data[$i]["ChannelName"]);
    if($Data[$i]["Status"] == "Downloading"){
      $ts=glob($WorkPath."/".$ChName."/stream/*.ts");
      $Count = count($ts);
      if($Count>0)$FileTime = filemtime($ts[0]);else $FileTime = 0;
      if(!file_exists("/proc/".$PID) || (
      file_exists("/proc/".$PID) && 
      !file_exists("/proc/".$FPID) && 
      300 <= time() - $FileTime &&
      $FileTime > 0
      )){
        echo "Reastarting: ".$ChanID."\r\n";
        $Msg="Channel: ".$Data[$i]["ChannelName"].", ID: ".$ChanID." restarted at: ".date("H:i A");
        $sql="insert into notification (Title, Msg, Sent, Status) values ('Auto restart', '$Msg', now(), 'New')";
        $App->DB->exec($sql);
        $App->StopDownload(array("ChanID" => $ChanID));
        $App->StartDownload(array("ChanID" => $ChanID));
      }
    }
  }
}

?>
