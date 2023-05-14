<?php
include "_config.php";
function GetClientIP(){
  if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
    $ip = $_SERVER['HTTP_CLIENT_IP'];
  } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
  } else {
    $ip = $_SERVER['REMOTE_ADDR'];
  }
  return $ip;
}
$Action=$_GET["action"];
switch($Action){
  case "m3u8":{
    $ClientIP = GetClientIP();
    $ChanID = $_GET["id"];
    if(is_numeric($ChanID)){
      $Channel = $App->GetChannel($ChanID);
    }else{
      $Channel = $App->GetChannelByName($ChanID);
      $ChanID = $Channel["ID"];
    }
    if($ClientIP=="127.0.0.1" || $ClientIP=="::1" || $ClientIP==$_SERVER["SERVER_ADDR"] || $App->AllowedIP($ChanID, $ClientIP)){
      $M3u8Path = $App->GetConfig("M3UDownloadURL");
      $ChName=str_replace(" ", "_", $Channel["ChannelName"]);
      $M3u8File = $M3u8Path."/".$ChName."/stream/index.m3u8";
      header("location: ".$M3u8File);
    }
    break;    
  }
}
?>
