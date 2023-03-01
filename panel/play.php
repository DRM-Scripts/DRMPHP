<?php
include "_db.php";
try {
  $db = new PDO('mysql:host='.$DBHost.';dbname='.$DBName.';charset=utf8', $DBUser, $DBPass);
} catch (PDOException $e) {
  print "Error!: " . $e->getMessage() . "<br/>";
  die();
}
function GetTSFiles($file){
  if (file_exists($file)) {
    $file = file_get_contents($file);
    if (preg_match_all("/(.*?).ts/", $file, $data)) {
      return $data[0];
    }
  }
  return false;
}
function ClientConnected(){
  if (connection_status() != CONNECTION_NORMAL || connection_aborted()) {
    return false;
  }
  return true;
}
function ReadConfig(){
  global $db;
  $sql="select * from config";
  $st=$db->prepare($sql);
  $st->execute();
  $data=$st->fetchAll();
  return $data;
}
function GetConfig($Config, $ConfigName){
  for($i=0;$i<count($Config);$i++){
    if($Config[$i]["ConfigName"]==$ConfigName){
      return $Config[$i]["ConfigValue"];
    }
  }
}
function GetChannel($ChID){
  global $db;
  $sql="select AudioID, VideoID, Manifest, KID, `Key`, VariantID, ChannelName
  from channels 
  where channels.ID=:ID";
  $st=$db->prepare($sql);
  $st->bindParam(":ID", $ChID);
  $st->execute();
  $line= $st->fetch();  
  return $line;
}
function readfile_chunked($filename) {
  $chunksize = 4*(1024); 
  $buffer = '';
  $cnt =0;
  $handle = fopen($filename, 'rb');
  if ($handle === false) {
    return false;
  }
  while (!feof($handle)) {
    $buffer = fread($handle, $chunksize);
    echo $buffer;
  }
  $status = fclose($handle);
}
ignore_user_abort(true);
set_time_limit(0);
//register_shutdown_function('shutdown');
$id=$_GET["id"];
if($id){
  header("Connection: close"); 
  header("Content-Encoding: none");
  header("Access-Control-Allow-Origin: *");
  header("Content-Transfer-Encoding: Binary");
  header("Content-Type: application/octet-stream");
  header('Expires: 0');
  header('Cache-Control: no-cache');
  header('Pragma: public');

  $ChData                     = GetChannel($id);
  $Config                     = ReadConfig();
  $ChName                     = str_replace(" ", "_", $ChData["ChannelName"]);
  $Config                     = ReadConfig();
  $WorkPath                   = GetConfig($Config, "DownloadPath");
  $PlaylistLimit              = GetConfig($Config, "PlaylistLimit");

  $folder = $WorkPath."/".$ChName."/stream/";
  $segments = 8 * 2;

  $Factor=0;
  $files = GetTSFiles($folder.'index.m3u8');

  while ($files = GetTSFiles($folder.'index.m3u8')) {
    for($i=1;$i<count($files);$i++) {
      $file=$files[$i];
      if (file_exists($folder . $file) && ClientConnected()) {
        //readfile_chunked($folder . $file);
        readfile($folder . $file);
      } else {
        exit();
      }
    }
    $getFile = array_pop($files);
    preg_match("/(.*)\\./", $getFile, $clean);

    $segment = intval($clean[1]);

    $t = 0;
    while (($t <= $segments) && ClientConnected()) {
      $next =str_pad($segment + 1,8,"0", STR_PAD_LEFT).".ts";
      $nextnext =str_pad($segment + 2,8,"0", STR_PAD_LEFT).".ts";
      if (!file_exists($folder . $next)) {
        sleep(1);
        $t++;
        continue;
      }    

      $t = 0;
      $fopen = fopen($folder . $next, "r");
      while (($t <= $segments) && ClientConnected() && !file_exists($folder . $nextnext)) {
        $line = stream_get_line($fopen, 4096);
        if (empty($line)) {
          sleep(1);
          ++$t;
          continue;
        }
        echo $line;
        $t = 0;
      } 
      echo stream_get_line($fopen, filesize($folder . $next) - ftell($fopen));
      fclose($fopen);
      $t = 0;
      $segment++;
    }
  }
}

?>
