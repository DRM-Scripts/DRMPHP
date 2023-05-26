<?php
include "_config.php";
$Type=file_get_contents("export.txt");
if($Type=="api" || $Type=="hls"){
  unlink("export.txt");
  $Data=$App->GetAllChannels();
  $DownloadURL = $App->GetConfig("M3UDownloadURL");
  for($i=0;$i<count($Data);$i++){
    if($Data[$i]["Status"]=="Downloading"){
      $ChName   = str_replace(" ", "_", $Data[$i]["ChannelName"]);
      if($Type=="api"){
        $urlbase = "http".(!empty($_SERVER['HTTPS'])?"s":"")."://".$_SERVER['SERVER_NAME']."";      
        $URL = $urlbase."/api.php?action=m3u8&id=".$ChName;
      }else{
        $URL = $DownloadURL."/".$ChName."/hls/index.m3u8";
      }
      $m3u8files.="#EXTINF:-1, ".$Data[$i]["ChannelName"].PHP_EOL.$URL.PHP_EOL;
    }
  }
  if($m3u8files)$m3u8files=rtrim($m3u8files,PHP_EOL);
  $m3u8="#EXTM3U".PHP_EOL;
  //."#EXT-X-VERSION:3".PHP_EOL
  //."#EXT-X-PLAYLIST-TYPE:VOD".PHP_EOL;
  $m3u8.=$m3u8files.PHP_EOL."#EXT-X-ENDLIST";
}
header("Content-type: application/x-mpegURL");
header("Content-Disposition: attachment; filename=list.m3u8");
echo $m3u8;
?>
