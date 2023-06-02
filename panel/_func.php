<?php
  include "_config.php";
  $Func=$_POST["Func"];
  switch($Func){
    case "Parse":{
      $App->Parse($_POST["ID"]);
      break;
    }
    case "TestMPD":{
      $Res=$App->TestMPD($_POST);
      echo $Res;
      break;
    }
    case "UpdateChanVariants":{
      $App->UpdateChanVariants($_POST);
      break;
    }
    case "SaveVariant":{
      $App->SaveVariant($_POST);
      break;
    }
    case "IniDownload":{
      //unlink("_collect_template.txt");
      break;
    }
    case "StartDownload":{
      $App->StartDownload($_POST);
      break;
    }
    case "StopDownload":{
      $App->StopDownload($_POST);
      break;
    }
    case "ExportAll":{
      file_put_contents("export.txt", $_POST["Type"]);
      break;
    }
    case "DeleteChannel":{
      $App->DeleteChannel($_POST["ID"]);
      break;
    }
    case "All":{
      $App->All($_POST["Action"]);
      break;
    }
    case "UpdateTable":{
      echo $App->GetChanStat();
      break;
    }
    case "DownloadBackup":{
      $App->DownloadBackup($_POST["File"]);
      break;
    }
    case "RestoreBackup":{
      $App->RestoreBackup($_POST["File"]);
      break;
    }
    case "DeleteBackup":{
      $App->DeleteBackup($_POST["File"]);
      break;
    }
    case "SetNotiSeen":{
      $App->SetNotiSeen($_POST["ID"]);
      break;
    }
    case "GetKID":{
      $kid=$App->GetKID($_POST);
      echo json_encode($kid);
      break;
    }
    case "DeleteCat":{
      $App->DeleteCat($_POST["ID"]);
      break;
    }
  }
?>
