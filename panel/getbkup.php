<?php
include "_config.php";
$x=file_get_contents("getbkup.txt");
unlink("getbkup.txt");
if($x){
  if(isset($_GET['file'])){
    $filename = $App->GetConfig("BackupPath")."/".$_GET['file'];
    if(file_exists($filename)) {
      header('Content-Description: File Transfer');
      header('Content-Type: application/octet-stream');
      header("Cache-Control: no-cache, must-revalidate");
      header("Expires: 0");
      header('Content-Disposition: attachment; filename="'.basename($filename).'"');
      header('Content-Length: ' . filesize($filename));
      header('Pragma: public');
      flush();
      readfile($filename);
      die();
    }else{
      echo "File does not exist.";
    }
  }else echo "Filename is not defined.";
}
?>