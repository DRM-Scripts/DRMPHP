<?php
class App
{
    public $DB;
    public $Config;

    public function __construct()
    {
        include "_db.php";
        try {
            $db = new PDO('mysql:host=' . $DBHost . ';dbname=' . $DBName . ';charset=utf8', $DBUser, $DBPass);
            $this->DB = $db;
        } catch (PDOException $e) {
            print "Error!: " . $e->getMessage() . "<br/>";
            die();
        }
        $this->ReadConfig();
    }

    public function ReadConfig()
    {
        $sql = "select * from config order by ID";
        $st = $this->DB->prepare($sql);
        $st->execute();
        $this->Config = $st->fetchAll();
    }

    public function GetConfig($ConfigName)
    {
        for ($i = 0; $i < count($this->Config); $i++) {
            if ($this->Config[$i]["ConfigName"] == $ConfigName) {
                return $this->Config[$i]["ConfigValue"];
            }
        }
    }

    public function LoggedIn()
    {
        if (intval($_SESSION["User"]["ID"]) > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function getCurrentUserId()
    {
        // Implement the logic to retrieve the current user's ID
        // You can use your own logic or database queries to fetch the user ID
        // Return the user ID

        // Example implementation using session
        if (isset($_SESSION['User']['ID'])) {
            return $_SESSION['User']['ID'];
        }

        return null;
    }

    public function Login($UserID, $Password)
    {
        $sql = "SELECT * FROM users WHERE UserID = :UserID";
        $st = $this->DB->prepare($sql);
        $st->bindParam(":UserID", $UserID);
        $st->execute();
        $line = $st->fetch();
        if ($line && $line["Password"] == $Password) {
            $sql = "UPDATE users SET LastAccess = :LastAccess WHERE UserID = :UserID";
            $st = $this->DB->prepare($sql);
            $st->bindParam(":LastAccess", date("Y-m-d H:i:s"));
            $st->bindParam(":UserID", $UserID);
            $st->execute();
            return $line;
        } else {
            return false;
        }
    }

    public function ChangePassword($UserID, $CurrentPassword, $NewPassword)
    {
        // Retrieve the user's stored password from the database
        $sql = "SELECT Password FROM users WHERE UserID = :UserID";
        $st = $this->DB->prepare($sql);
        $st->bindParam(":UserID", $UserID);
        $st->execute();
        $line = $st->fetch();

        if ($line && $line["Password"] === $CurrentPassword) {
            try {
                // Update the password
                $sql = "UPDATE users SET Password = :NewPassword WHERE UserID = :UserID";
                $st = $this->DB->prepare($sql);
                $st->bindParam(":NewPassword", $NewPassword);
                $st->bindParam(":UserID", $UserID);
                $st->execute();

                // Optionally, update the last access time
                $sql = "UPDATE users SET LastAccess = :LastAccess WHERE UserID = :UserID";
                $st = $this->DB->prepare($sql);
                $st->bindParam(":LastAccess", date("Y-m-d H:i:s"));
                $st->bindParam(":UserID", $UserID);
                $st->execute();

                return true; // Password change successful

            } catch (PDOException $e) {
                // Handle the database error
                echo "Database Error: " . $e->getMessage();
                return false; // Password change failed
            }
        } else {
            return false; // Current password is incorrect
        }
    }

    public function GetChannel($ID)
    {
        $sql = "select * from channels where ID=:ID";
        $st = $this->DB->prepare($sql);
        $st->bindParam(":ID", $ID);
        $st->execute();
        $Chan = $st->fetch();
        $Chan["AllowedIPJson"] = $Chan["AllowedIP"];
        $tmp = json_decode($Chan["AllowedIP"], true);
        $Chan["AllowedIP"] = implode("\r\n", $tmp);

        $sql = "select distinct AudioID from variant where ChannelID=:ChannelID";
        $st = $this->DB->prepare($sql);
        $st->bindParam(":ChannelID", $ID);
        $st->execute();
        $Chan["AudioIDs"] = $st->fetchAll();

        $sql = "select distinct VideoID from variant where ChannelID=:ChannelID";
        $st = $this->DB->prepare($sql);
        $st->bindParam(":ChannelID", $ID);
        $st->execute();
        $Chan["VideoIDs"] = $st->fetchAll();

        $keySql = "select * from channel_keys where ChannelID=:ChannelID";
        $st = $this->DB->prepare($keySql);
        $st->bindParam(":ChannelID", $ID);
        $st->execute();
        $Chan["Keys"] = $st->fetchAll();

        $headerSql = "select * from channel_headers where ChannelID=:ChannelID";
        $st = $this->DB->prepare($headerSql);
        $st->bindParam(":ChannelID", $ID);
        $st->execute();
        $Chan["CustomHeaders"] = $st->fetchAll();

        return $Chan;
    }

    public function GetChannelByName($ChName)
    {
        $sql = "select * from channels where REPLACE(ChannelName, ' ', '_') = '$ChName'";
        $st = $this->DB->prepare($sql);
        $st->bindParam(":ID", $ID);
        $st->execute();
        $Chan = $st->fetch();
        $Chan["AllowedIPJson"] = $Chan["AllowedIP"];
        $tmp = json_decode($Chan["AllowedIP"], true);
        $Chan["AllowedIP"] = implode("\r\n", $tmp);

        $sql = "select distinct AudioID from variant where ChannelID=:ChannelID";
        $st = $this->DB->prepare($sql);
        $st->bindParam(":ChannelID", $ID);
        $st->execute();
        $Chan["AudioIDs"] = $st->fetchAll();

        $sql = "select distinct VideoID from variant where ChannelID=:ChannelID";
        $st = $this->DB->prepare($sql);
        $st->bindParam(":ChannelID", $ID);
        $st->execute();
        $Chan["VideoIDs"] = $st->fetchAll();

        $keySql = "select * from channel_keys where ChannelID=:ChannelID";
        $st = $this->DB->prepare($keySql);
        $st->bindParam(":ChannelID", $ID);
        $st->execute();
        $Chan["Keys"] = $st->fetchAll();

        $headerSql = "select * from channel_headers where ChannelID=:ChannelID";
        $st = $this->DB->prepare($headerSql);
        $st->bindParam(":ChannelID", $ID);
        $st->execute();
        $Chan["CustomHeaders"] = $st->fetchAll();

        return $Chan;
    }

    public function GetVariants($ChannelID)
    {
        $sql = "select * from variant where ChannelID=:ChannelID order by AudioID, VideoID";
        $st = $this->DB->prepare($sql);
        $st->bindParam(":ChannelID", $ChannelID);
        $st->execute();
        $Variants = $st->fetchAll();
        return $Variants;
    }

    public function GetAudioIDs($ChannelID)
    {
        $sql = "select distinct  AudioID, Language from variant where ChannelID=:ChannelID
    group by  AudioID, Language";
        $st = $this->DB->prepare($sql);
        $st->bindParam(":ChannelID", $ChannelID);
        $st->execute();
        $Variants = $st->fetchAll();
        return $Variants;
    }

    public function GetAllChannels($Search = null)
    {
        if ($Search != null && $Search["search"]) {
            if ($Search["SearchChanName"]) {
                $Cond1 = " and ChannelName like '%" . $Search["SearchChanName"] . "%'";
            }
            if ($Search["SearchCatName"]) {
                $Cond2 = " and CatName like '%" . $Search["SearchCatName"] . "%'";
            }
            if ($Search["SearchMPDUrl"]) {
                $Cond3 = " and Manifest like '%" . $Search["SearchMPDUrl"] . "%'";
            }
        }
        $sql = "select channels.*, TIMEDIFF(now(), channels.StartTime) as Uptime, cats.CatName
    from channels
    inner join cats on channels.CatID = cats.CatID
    where 1=1 $Cond1 $Cond2 $Cond3
    order by ID";
        $st = $this->DB->prepare($sql);
        $st->execute();
        return $st->fetchAll();
    }

    public function SaveChannel($Data)
    {
        $ID = intval($Data["ID"]);
        $ChannelName = $Data["ChannelName"];
        $Manifest = $Data["Manifest"];
        $CatId = intval($Data["CatID"]);
        $KID = $Data["KID"];
        $Key = $Data["Key"];
        $CustomHeaders = $Data["customHeaders"];
        $AllowedIP = explode("\r\n", $Data["AllowedIP"]);
        $AllowedIPJson = json_encode($AllowedIP);
        $AutoRestart = intval($Data["AutoRestart"]);
        $AudioIDs = implode(",", $Data["AudioIDs"]);
        $Output = "hls";

        $UseProxy = intval($Data["UseProxy"]);
        $ProxyURL = $Data["ProxyURL"];
        $ProxyPort = intval($Data["ProxyPort"]);
        $ProxyUser = $Data["ProxyUser"];
        $ProxyPass = $Data["ProxyPass"];

        $DownloadUseragent = $Data["DownloadUseragent"];
        //$AudioID        = $Data["AudioID"];
        $VideoID = $Data["VideoID"];

        $SegmentJoiner = intval($Data["SegmentJoiner"]);
        $PlaylistLimit = intval($Data["PlaylistLimit"]);
        $URLListLimit = intval($Data["URLListLimit"]);

        if (count($KID) != count($Key)) {
            return "KID and Key count not match";
        }

        if ($SegmentJoiner < 3) {
            $SegmentJoiner = $this->GetConfig("SegmentJoiner");
        }
        if ($PlaylistLimit < 3) {
            $PlaylistLimit = $this->GetConfig("PlaylistLimit");
        }
        if ($URLListLimit < 1) {
            $URLListLimit = $this->GetConfig("URLListLimit");
        }

        if ($ID == 0) {
            $sql = "insert into channels (
      `ChannelName`, `Manifest`, `CatId`, `SegmentJoiner`, `PlaylistLimit`, `URLListLimit`, `DownloadUseragent`, `AudioID`, `VideoID`, `AllowedIP`, `Output`, `UseProxy`, `ProxyURL`, `ProxyPort`, `ProxyUser`, `ProxyPass`
      ) values (
      :ChannelName, :Manifest, :CatId, :SegmentJoiner, :PlaylistLimit, :URLListLimit, :DownloadUseragent, :AudioID, :VideoID, :AllowedIP, :Output, :UseProxy, :ProxyURL, :ProxyPort, :ProxyUser, :ProxyPass
      )";
            $st = $this->DB->prepare($sql);
            $st->bindParam(":ChannelName", $ChannelName);
            $st->bindParam(":Manifest", $Manifest);
            $st->bindParam(":CatId", $CatId);
            $st->bindParam(":SegmentJoiner", $SegmentJoiner);
            $st->bindParam(":PlaylistLimit", $PlaylistLimit);
            $st->bindParam(":URLListLimit", $URLListLimit);
            $st->bindParam(":DownloadUseragent", $DownloadUseragent);
            $st->bindParam(":AudioID", $AudioIDs);
            $st->bindParam(":VideoID", $VideoID);
            $st->bindParam(":AllowedIP", $AllowedIPJson);
            $st->bindParam(":Output", $Output);
            $st->bindParam(":UseProxy", $UseProxy);
            $st->bindParam(":ProxyURL", $ProxyURL);
            $st->bindParam(":ProxyPort", $ProxyPort);
            $st->bindParam(":ProxyUser", $ProxyUser);
            $st->bindParam(":ProxyPass", $ProxyPass);
            $st->execute();
            $ID = $this->DB->lastInsertId();

            if ($ID == 0) {
                return "Error while inserting channel";
            }
            $keySql = "insert into channel_keys (ChannelID, KID, `Key`) values (:ChannelID, :KID, :Key)";
            for ($i = 0; $i < count($KID); $i++) {
                if ($KID[$i] == "" || $Key[$i] == "") {
                    continue;
                }

                $st = $this->DB->prepare($keySql);
                $st->bindParam(":ChannelID", $ID);
                $st->bindParam(":KID", $KID[$i]);
                $st->bindParam(":Key", $Key[$i]);
                $st->execute();
            }

            $headerSql = "insert into channel_headers (ChannelID, `Value`) values (:ChannelID, :Value)";
            for ($i = 0; $i < count($CustomHeaders); $i++) {
                if ($CustomHeaders[$i] == "") {
                    continue;
                }

                $st = $this->DB->prepare($headerSql);
                $st->bindParam(":ChannelID", $ID);
                $st->bindParam(":Value", $CustomHeaders[$i]);
                $st->execute();
            }

            $this->Parse($ID);
        } else {
            $Old = $this->GetChannel($ID);
            $ManifestField = "";
            if ($Old["Manifest"] != $Manifest) {
                $ManifestField = ", `Manifest`      =:Manifest";
            }

            $sql = "update channels set
      `ChannelName`     =:ChannelName
      $ManifestField
      , `SegmentJoiner` =:SegmentJoiner
      , `PlaylistLimit` =:PlaylistLimit
      , `URLListLimit`  =:URLListLimit
      , `DownloadUseragent`=:DownloadUseragent
      , `AudioID`=:AudioID
      , `VideoID`=:VideoID
      , `AllowedIP`=:AllowedIP
      , `Output`=:Output
      , `UseProxy`=:UseProxy
      , `ProxyURL`=:ProxyURL
      , `ProxyPort`=:ProxyPort
      , `ProxyUser`=:ProxyUser
      , `ProxyPass`=:ProxyPass
      , `AutoRestart`=:AutoRestart
      , `CatId`=:CatId
      where ID=:ID";
            $st = $this->DB->prepare($sql);
            $st->bindParam(":ID", $ID);
            if ($ManifestField) {
                $st->bindParam(":Manifest", $Manifest);
            }
            $st->bindParam(":ChannelName", $ChannelName);
            $st->bindParam(":SegmentJoiner", $SegmentJoiner);
            $st->bindParam(":PlaylistLimit", $PlaylistLimit);
            $st->bindParam(":URLListLimit", $URLListLimit);
            $st->bindParam(":DownloadUseragent", $DownloadUseragent);
            $st->bindParam(":AudioID", $AudioIDs);
            $st->bindParam(":VideoID", $VideoID);
            $st->bindParam(":AllowedIP", $AllowedIPJson);
            $st->bindParam(":Output", $Output);
            $st->bindParam(":UseProxy", $UseProxy);
            $st->bindParam(":ProxyURL", $ProxyURL);
            $st->bindParam(":ProxyPort", $ProxyPort);
            $st->bindParam(":ProxyUser", $ProxyUser);
            $st->bindParam(":ProxyPass", $ProxyPass);
            $st->bindParam(":AutoRestart", $AutoRestart);
            $st->bindParam(":CatId", $CatId);
            $st->execute();

            $removeExistingKeysSql = "DELETE FROM channel_keys WHERE ChannelID=:ChannelID";
            $st = $this->DB->prepare($removeExistingKeysSql);
            $st->bindParam(":ChannelID", $ID);
            $st->execute();

            for ($i = 0; $i < count($KID); $i++) {
                $keyId = $KID[$i];
                $contentKey = $Key[$i];
                if ($keyId == "" || $contentKey == "") {
                    continue;
                }

                $insertKeySql = "INSERT INTO channel_keys (ChannelID, KID, `Key`) VALUES (:ChannelID, :KID, :Key)";
                $st = $this->DB->prepare($insertKeySql);
                $st->bindParam(":ChannelID", $ID);
                $st->bindParam(":KID", $keyId);
                $st->bindParam(":Key", $contentKey);
                $st->execute();
            }

            $removeExistingHeadersSql = "DELETE FROM channel_headers WHERE ChannelID=:ChannelID";
            $st = $this->DB->prepare($removeExistingHeadersSql);
            $st->bindParam(":ChannelID", $ID);
            $st->execute();

            for ($i = 0; $i < count($CustomHeaders); $i++) {
                $Value = $CustomHeaders[$i];
                if ($Value == "") {
                    continue;
                }
                $insertHeaderSql = "INSERT INTO channel_headers (ChannelID, `Value`) VALUES (:ChannelID, :Value)";
                $st = $this->DB->prepare($insertHeaderSql);
                $st->bindParam(":ChannelID", $ID);
                $st->bindParam(":Value", $Value);
                $st->execute();
            }

            if ($ManifestField) {
                $Data["ChanID"] = $ID;
                $this->StopDownload($Data);
                $this->Parse($ID);
            }
        }
        return $ID;
    }

    public function Parse($ID)
    {
        $Data = $this->GetChannel($ID);
        $UseProxy = intval($Data["UseProxy"]) == 1;
        if ($UseProxy) {
            $ProxyURL = $Data["ProxyURL"];
            if ($ProxyURL) {
                $ProxyPort = $Data["ProxyPort"];
                $ProxyUser = $Data["ProxyUser"];
                $ProxyPass = $Data["ProxyPass"];
            } else {
                $ProxyURL = $this->GetConfig("ProxyURL");
                $ProxyPort = $this->GetConfig("ProxyPort");
                $ProxyUser = $this->GetConfig("ProxyUser");
                $ProxyPass = $this->GetConfig("ProxyPass");
            }
            $cmd = "php downloader.php --mode=infoshort --chid=$ID --proxyurl=$ProxyURL --proxyport=$ProxyPort --proxyuser=$ProxyUser --proxypass=$ProxyPass";
        } else {
            $cmd = "php downloader.php --mode=infoshort --chid=$ID";
        }

        exec($cmd, $Res);
        for ($i = 0; $i < count($Res); $i++) {
            $Res[$i] = explode("|", $Res[$i]);
            $Variants[$i]["Language"] = $Res[$i][0];
            $Variants[$i]["Bandwidth"] = "0";
            $Variants[$i]["AudioID"] = $Res[$i][1];
            $Variants[$i]["AudioBandwidth"] = $Res[$i][2];
            $Variants[$i]["AudioCodecs"] = $Res[$i][3];
            $Variants[$i]["VideoID"] = $Res[$i][4];
            $Variants[$i]["VideoBandwidth"] = $Res[$i][5];
            $Variants[$i]["VideoCodecs"] = $Res[$i][6];
            $Variants[$i]["Width"] = $Res[$i][7];
            $Variants[$i]["Height"] = $Res[$i][8];
            $Variants[$i]["Framerate"] = $Res[$i][9];
        }
        $Data["ChanID"] = $ID;
        $Data["Variants"] = $Variants;
        $this->UpdateChanVariants($Data);
    }

    public function UpdateChanVariants($Data)
    {
        $ChanID = $Data["ChanID"];
        $Variants = $Data["Variants"];

        $Chan = $this->GetChannel($ChanID);
        $OldAudioID = $Chan["AudioID"];
        $OldVideoID = $Chan["VideoID"];

        $sql = "delete from variant where ChannelID=:ChannelID";
        $st = $this->DB->prepare($sql);
        $st->bindParam(":ChannelID", $ChanID);
        $st->execute();
        for ($i = 0; $i < count($Variants); $i++) {
            $Variant = $Variants[$i];
            $sql = "insert into variant(
      ChannelID, Language, Bandwidth, AudioID, AudioBandwidth, AudioCodecs, VideoID, VideoBandwidth, VideoCodecs, Width, Height, Framerate
      ) values (
      :ChannelID, :Language, :Bandwidth, :AudioID, :AudioBandwidth, :AudioCodecs, :VideoID, :VideoBandwidth, :VideoCodecs, :Width, :Height, :Framerate
      )";

            $Language = $Variant["Language"];
            $Bandwidth = $Variant["Bandwidth"];
            $AudioID = $Variant["AudioID"];
            $AudioBandwidth = $Variant["AudioBandwidth"];
            $AudioCodecs = $Variant["AudioCodecs"];
            $VideoID = $Variant["VideoID"];
            $VideoBandwidth = $Variant["VideoBandwidth"];
            $VideoCodecs = $Variant["VideoCodecs"];
            $Width = $Variant["Width"];
            $Height = $Variant["Height"];
            $Framerate = $Variant["Framerate"];

            $st = $this->DB->prepare($sql);
            $st->bindParam(":ChannelID", $ChanID);
            $st->bindParam(":Language", $Language);
            $st->bindParam(":Bandwidth", $Bandwidth);
            $st->bindParam(":AudioID", $AudioID);
            $st->bindParam(":AudioBandwidth", $AudioBandwidth);
            $st->bindParam(":AudioCodecs", $AudioCodecs);
            $st->bindParam(":VideoID", $VideoID);
            $st->bindParam(":VideoBandwidth", $VideoBandwidth);
            $st->bindParam(":VideoCodecs", $VideoCodecs);
            $st->bindParam(":Width", $Width);
            $st->bindParam(":Height", $Height);
            $st->bindParam(":Framerate", $Framerate);
            $st->execute();
        }

        if ($OldAudioID && $OldVideoID) {
            $sql = "select ID from variant where ChannelID=:ChannelID and AudioID=:AudioID and VideoID=:VideoID";
            $st = $this->DB->prepare($sql);
            $st->bindParam(":ChannelID", $ChanID);
            $st->bindParam(":AudioID", $OldAudioID);
            $st->bindParam(":VideoID", $OldVideoID);
            $st->execute();
            $line = $st->fetch();
            if (intval($line["ID"]) == 0) {
                $sql = "update channels set AudioID='', VideoID='' where ID=:ID";
                $st->bindParam(":ID", $ChanID);
                $st->execute();
            }
        }
    }

    public function SaveVariant($Data)
    {
        $ID = $Data["ChanID"];
        $Variant = $Data["Variant"];
        $tmp = explode("|", $Variant);
        $AudioID = $tmp[0];
        $VideoID = $tmp[1];
        $sql = "update channels set AudioID=:AudioID, VideoID=:VideoID where ID=:ID";
        $st = $this->DB->prepare($sql);
        $st->bindParam(":AudioID", $AudioID);
        $st->bindParam(":VideoID", $VideoID);
        $st->bindParam(":ID", $ID);
        $st->execute();
    }

    public function StartDownload($Data)
    {
        $ChanID = $Data["ChanID"];
        $DownloaderPath = $this->GetConfig("DownloaderPath");
        $ChannData = $this->GetChannel($ChanID);
        $UseProxy = intval($ChannData["UseProxy"]) == 1;
        if ($UseProxy) {
            $ProxyURL = $ChannData["ProxyURL"];
            if ($ProxyURL) {
                $ProxyPort = $ChannData["ProxyPort"];
                $ProxyUser = $ChannData["ProxyUser"];
                $ProxyPass = $ChannData["ProxyPass"];
            } else {
                $ProxyURL = $this->GetConfig("ProxyURL");
                $ProxyPort = $this->GetConfig("ProxyPort");
                $ProxyUser = $this->GetConfig("ProxyUser");
                $ProxyPass = $this->GetConfig("ProxyPass");
            }
            $cmd = "sudo php $DownloaderPath/downloader.php --mode=download --chid=$ChanID --proxyurl=$ProxyURL --proxyport=$ProxyPort --proxyuser=$ProxyUser --proxypass=$ProxyPass --checkkey=1";
        } else {
            $cmd = "sudo php $DownloaderPath/downloader.php --mode=download --chid=$ChanID --checkkey=1";
        }
        $this->execInBackground($cmd);
        sleep(1);
    }

    public function execInBackground($cmd)
    {
        if (substr(php_uname(), 0, 7) == "Windows") {
            popen("start /B " . $cmd, "r");
        } else {
            exec($cmd . " > /dev/null &");
        }
    }

    public function StopDownload($Data)
    {
        $ChanID = $Data["ChanID"];
        $tmp = $this->GetChannel($ChanID);
        $ChName = $tmp["ChannelName"];
        $PID = $tmp["PID"];
        $FPID = $tmp["FPID"];

        if ($PID) {
            if (substr(php_uname(), 0, 7) == "Windows") {
                exec("taskkill /PID $PID /F");
            } else {
                exec("sudo kill -9 $PID");
            }
        }
        if ($FPID) {
            if (substr(php_uname(), 0, 7) == "Windows") {
                exec("taskkill /PID $FPID /F");
            } else {
                exec("sudo kill -9 $FPID");
            }
        }

        $sql = "update channels set Status=:Status, PID=0, FPID=0, info='', StartTime=null, EndTime='" . date("Y-m-d H:i:s") . "', info='' where ID=:ID";
        $Status = "Stopped";
        $st = $this->DB->prepare($sql);
        $st->bindParam(":ID", $ChanID);
        $st->bindParam(":Status", $Status);
        $st->execute();
        $WorkPath = $this->GetConfig("DownloadPath");
        $ChName = str_replace(" ", "_", $ChName);
        array_map('unlink', array_filter((array) glob($WorkPath . "/" . $ChName . "/seg/*")));
        array_map('unlink', array_filter((array) glob($WorkPath . "/" . $ChName . "/stream/*")));
        array_map('unlink', array_filter((array) glob($WorkPath . "/" . $ChName . "/ts/*")));
        array_map('unlink', array_filter((array) glob($WorkPath . "/" . $ChName . "/log/*")));
        array_map('unlink', array_filter((array) glob($WorkPath . "/" . $ChName . "/hls/*")));
        array_map('unlink', array_filter((array) glob($WorkPath . "/" . $ChName . "/*")));
    }

    public function SaveSettings($Data)
    {
        foreach ($Data as $Key => $Value) {
            $Value = addslashes($Value);
            $sql = "update config set ConfigValue='$Value' where ConfigName='$Key'";
            $this->DB->exec($sql);
        }
        $this->ReadConfig();
    }

    public function DeleteChannel($ID)
    {
        $ChanID = $ID;
        $this->StopDownload(array("ChanID" => $ChanID));
        $sql = "delete from channels where ID=:ID";
        $st = $this->DB->prepare($sql);
        $st->bindParam(":ID", $ChanID);
        $st->execute();

        $sql = "delete from variant where ChannelID=:ID";
        $st = $this->DB->prepare($sql);
        $st->bindParam(":ID", $ChanID);
        $st->execute();

        $sql = "delete from channel_keys where ChannelID=:ID";
        $st = $this->DB->prepare($sql);
        $st->bindParam(":ID", $ChanID);
        $st->execute();

        $sql = "delete from channel_headers where ChannelID=:ID";
        $st = $this->DB->prepare($sql);
        $st->bindParam(":ID", $ChanID);
        $st->execute();
    }

    public function All($Action)
    {
        $Chan = $this->GetAllChannels();

        for ($i = 0; $i < count($Chan); $i++) {
            $Data["ChanID"] = $Chan[$i]["ID"];
            if ($Action == "Start") {
                if ($Chan[$i]["Status"] == "Stopped") {
                    $this->StartDownload($Data);
                }
            }
            if ($Action == "Stop") {
                $this->StopDownload($Data);
            }
        }
    }

    public function TestMPD($Data)
    {
        $Url = $Data["MPD"];
        $UseProxy = $Data["UseProxy"] == "true";
        $Useragent = $Data["Useragent"];
        if ($Useragent == "") {
            $Useragent = $this->GetConfig("DownloadUseragent");
        }

        if ($UseProxy) {
            $ProxyURL = $Data["ProxyURL"];
            if ($ProxyURL) {
                $ProxyPort = $Data["ProxyPort"];
                $ProxyUser = $Data["ProxyUser"];
                $ProxyPass = $Data["ProxyPass"];
            } else {
                $ProxyURL = $this->GetConfig("ProxyURL");
                $ProxyPort = $this->GetConfig("ProxyPort");
                $ProxyUser = $this->GetConfig("ProxyUser");
                $ProxyPass = $this->GetConfig("ProxyPass");
            }
            $cmd = 'php downloader.php --mode=testonly --mpdurl="' . $Url . '" --proxyurl="' . $ProxyURL . '" --proxyport="' . $ProxyPort . '" --proxyuser="' . $ProxyUser . '" --proxypass="' . $ProxyPass . '" --useragent="' . $Useragent . '"';
        } else {
            $cmd = 'php downloader.php --mode=testonly --mpdurl="' . $Url . '"';
        }
        exec($cmd, $Res);
        $data["str"] = implode("\r\n", $Res);

        $Res = null;
        if ($UseProxy) {
            $ProxyURL = $Data["ProxyURL"];
            if ($ProxyURL) {
                $ProxyPort = $Data["ProxyPort"];
                $ProxyUser = $Data["ProxyUser"];
                $ProxyPass = $Data["ProxyPass"];
            } else {
                $ProxyURL = $this->GetConfig("ProxyURL");
                $ProxyPort = $this->GetConfig("ProxyPort");
                $ProxyUser = $this->GetConfig("ProxyUser");
                $ProxyPass = $this->GetConfig("ProxyPass");
            }
            $cmd = 'php downloader.php --mode=infojson --mpdurl="' . $Url . '"  --proxyurl="' . $ProxyURL . '" --proxyport="' . $ProxyPort . '" --proxyuser="' . $ProxyUser . '" --proxypass="' . $ProxyPass . '" --useragent="' . $Useragent . '"';
        } else {
            $cmd = 'php downloader.php --mode=infojson --mpdurl="' . $Url . '"';
        }
        exec($cmd, $Res);
        $x = json_decode($Res[0], true);
        $data["a"] = $x["a"];
        $data["v"] = $x["v"];
        return json_encode($data);
    }

    public function GetLog($ID, $Lines)
    {
        $data = $this->GetChannel($ID);
        $ChName = str_replace(" ", "_", $data["ChannelName"]);
        $WorkPath = $this->GetConfig("DownloadPath");
        $LogFile = $WorkPath . "/" . $ChName . "/log/ffmpeg.log";
        $Log[0] = $this->tail($LogFile, $Lines);

        $LogFile = $WorkPath . "/" . $ChName . "/log/php.log";
        $Log[1] = $this->tail($LogFile, $Lines);
        return $Log;
    }

    public function tail($filepath, $lines = 1, $adaptive = true)
    {
        $f = @fopen($filepath, "rb");
        if ($f === false) {
            return false;
        }

        if (!$adaptive) {
            $buffer = 4096;
        } else {
            $buffer = ($lines < 2 ? 64 : ($lines < 10 ? 512 : 4096));
        }

        fseek($f, -1, SEEK_END);
        if (fread($f, 1) != "\n") {
            $lines -= 1;
        }

        $output = '';
        $chunk = '';
        while (ftell($f) > 0 && $lines >= 0) {
            $seek = min(ftell($f), $buffer);
            fseek($f, -$seek, SEEK_CUR);
            $output = ($chunk = fread($f, $seek)) . $output;
            fseek($f, -mb_strlen($chunk, '8bit'), SEEK_CUR);
            $lines -= substr_count($chunk, "\n");
        }
        while ($lines++ < 0) {
            $output = substr($output, strpos($output, "\n") + 1);
        }
        fclose($f);
        return trim($output);
    }

    public function GetChanStat()
    {
        $sql = "select ID, TIMEDIFF(now(), channels.StartTime) as Uptime, info, status, PID, FPID from channels";
        $st = $this->DB->prepare($sql);
        $st->execute();
        $data = $st->fetchAll();
        for ($i = 0; $i < count($data); $i++) {
            if (file_exists("/proc/" . $data[$i]["PID"])) {
                $PIDExist = 1;
            } else {
                $PIDExist = 0;
            }

            if (file_exists("/proc/" . $data[$i]["FPID"])) {
                $FPIDExist = 1;
            } else {
                $FPIDExist = 0;
            }

            $x["id"] = $data[$i]["ID"];
            $x["status"] = $data[$i]["status"];
            $Info = json_decode($data[$i]["info"], true);
            $x["uptime"] = $data[$i]["Uptime"];
            $x["pid"] = $data[$i]["PID"];
            $x["fpid"] = $data[$i]["FPID"];
            $x["pidexist"] = $PIDExist;
            $x["fpidexist"] = $FPIDExist;
            $x["framerate"] = str_replace("/1", "", $Info["framerate"]);
            if ($x["status"] == "Downloading") {
                $x["bitrate"] = round($Info["bitrate"] / 1000, 1) . "kb";
                $x["codecs"] = $Info["vcodec"] . "/" . $Info["acodec"];
                $x["res"] = $Info["width"] . "x" . $Info["height"];
            } else {
                $x["bitrate"] = "";
                $x["codecs"] = "";
                $x["res"] = "";
            }
            $Stat[] = $x;
        }
        return json_encode($Stat);
    }

    public function AllowedIP($ChID, $IP)
    {
        $data = $this->GetChannel($ChID);
        $AllowedIPs = json_decode($data["AllowedIPJson"], true);
        for ($i = 0; $i < count($AllowedIPs); $i++) {
            if (strtolower($AllowedIPs[$i]) == "any" || $AllowedIPs[$i] == "*" || $AllowedIPs[$i] == $IP) {
                return true;
            }
        }
        return false;
    }

    public function BackupDatabase()
    {
        try {
            include "_db.php";
            $FolderName = $this->GetConfig("BackupPath");
            $cmd = "sudo mkdir $FolderName";
            exec($cmd);
            $cmd = "sudo chown -R www-data:www-data $FolderName";
            exec($cmd);
            $cmd = "sudo chmod -R 777 $FolderName";
            exec($cmd);

            $FileName = $DBName . "_" . date("Y-m-d_H:i:s", time()) . ".sql";
            $cmd = "mysqldump --add-drop-table -u $DBUser -p'$DBPass' $DBName > $FolderName/" . $FileName;
            exec($cmd);
            $SQL = file_get_contents($FolderName . "/" . $FileName);
            $Ret[0] = $FolderName;
            $Ret[1] = $FileName;
        } catch (Exception $e) {
            $Ret[0] = "Error";
            $Ret[1] = $e->message;
        }
        return $Ret;
    }

    public function RestoreDatabase($Files)
    {

    }

    public function GetBackups()
    {
        $Folder = $this->GetConfig("BackupPath");
        $Files = glob($Folder . "/*.sql");
        for ($i = 0; $i < count($Files); $i++) {
            $Files[$i] = str_replace($Folder . "/", "", $Files[$i]);
        }
        return $Files;
    }

    public function DeleteBackup($File)
    {
        $Folder = $this->GetConfig("BackupPath");
        unlink($Folder . "/" . $File);
    }

    public function DownloadBackup($File)
    {
        file_put_contents("getbkup.txt", 1);
    }

    public function RestoreBackup($File)
    {
        try {
            include "_db.php";
            $Folder = $this->GetConfig("BackupPath");
            $cmd = "mysql -u $DBUser -p$DBPass $DBName < " . $Folder . "/" . $File;
            exec($cmd, $res);
        } catch (Exception $e) {
            $Ret[0] = "Error";
            $Ret[1] = $e->message;
        }
    }

    public function GetAllCats()
    {
        $sql = "select cats.CatID, cats.CatName, count(channels.ID) as ChannelsCount
    From cats left outer join channels on cats.CatID = channels.CatID
    group by cats.CatID, cats.CatName
    order by CatID";
        $st = $this->DB->prepare($sql);
        $st->execute();
        return $st->fetchAll();
    }

    public function GetCat($ID)
    {
        $sql = "select * From cats where CatID=:CatID";
        $st = $this->DB->prepare($sql);
        $st->bindParam(":CatID", $ID);
        $st->execute();
        return $st->fetch();
    }

    public function SaveCat($Data)
    {
        $ID = intval($Data["ID"]);
        $CatName = $Data["CatName"];
        if ($ID > 0) {
            $sql = "update cats set CatName=:CatName where CatID=:CatID";
            $st = $this->DB->prepare($sql);
            $st->bindParam(":CatID", $ID);
            $st->bindParam(":CatName", $CatName);
            $st->execute();
        } else {
            $sql = "Insert into cats (CatName) values (:CatName)";
            $st = $this->DB->prepare($sql);
            $st->bindParam(":CatName", $CatName);
            $st->execute();
            $ID = $this->DB->lastInsertId();
        }
        return $ID;
    }

    public function DeleteCat($ID)
    {
        if ($ID != 1) {
            $sql = "delete from cats where CatID=:CatID";
            $st = $this->DB->prepare($sql);
            $st->bindParam(":CatID", $ID);
            $st->execute();

            $sql = "update channels set CatID = 1 Where CatID =:CatID";
            $st = $this->DB->prepare($sql);
            $st->bindParam(":CatID", $ID);
            $st->execute();

            $Cats = $this->GetAllCats();
            if (!$Data) {
                $sql = "insert into cats (CatID, CatName) values (1, 'Uncategorized')";
                $st = $this->DB->prepare($sql);
                $st->execute();
            }
        }
    }

    public function GetStat()
    {
        $sql = "select Status, ChannelName, ifnull(TIME_TO_SEC(TIMEDIFF(now(), channels.StartTime)), 0)/60  as Uptime from channels";
        $st = $this->DB->prepare($sql);
        $st->execute();
        $data = $st->fetchAll();
        $Res["Total"] = count($data);
        $Res["Online"] = 0;
        $Res["Offline"] = 0;

        for ($i = 0; $i < count($data); $i++) {
            $Res["Names"] .= '"' . $data[$i]["ChannelName"] . '",';
            $Res["Uptime"] .= '"' . $data[$i]["Uptime"] . '",';
            if ($data[$i]["Status"] == "Downloading") {
                $Res["Online"]++;
            } else {
                $Res["Offline"]++;
            }
        }
        if ($Res["Names"]) {
            $Res["Names"] = rtrim($Res["Names"], ",");
        }

        if ($Res["Uptime"]) {
            $Res["Uptime"] = rtrim($Res["Uptime"], ",");
        }

        return $Res;
    }

    public function time_elapsed_string($datetime, $full = false)
    {
        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = array(
            'y' => 'year',
            'm' => 'month',
            'w' => 'week',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        );
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
            } else {
                unset($string[$k]);
            }
        }

        if (!$full) {
            $string = array_slice($string, 0, 1);
        }

        return $string ? implode(', ', $string) . ' ago' : 'just now';
    }

    public function GetNotification($Status = "")
    {
        if ($Status == "") {
            $Cond = "";
        } else { $Cond = " and Status='$Status'";}
        $sql = "select * from notification where 1=1 $Cond";
        $st = $this->DB->prepare($sql);
        $st->execute();
        $data = $st->fetchAll();
        for ($i = 0; $i < count($data); $i++) {
            $data[$i]["ago"] = $this->time_elapsed_string($data[$i]["Sent"]);
        }
        return $data;
    }

    public function SetNotiSeen($ID)
    {
        $sql = "update notification set Status='Seen' where ID=$ID";
        $this->DB->exec($sql);
    }

    public function GetFreeUDPIPs($ChID)
    {
        for ($j = 0; $j < 5; $j++) {
            for ($i = 1; $i < 256; $i++) {
                $All[] = "239.200.$j.$i";
            }
        }
        $sql = "select distinct UDPIP as IP from channels where ID <> $ChID";
        $st = $this->DB->prepare($sql);
        $st->execute();
        $Used = $st->fetchAll();
        for ($i = 0; $i < count($Used); $i++) {
            if (in_array($Used[$i]["IP"], $All)) {
                $index = array_search($Used[$i]["IP"], $All);
                unset($All[$index]);
            }
        }
        return $All;
    }

    private function GetURL($URL, $UseProxy = false, $ProxyURL = "", $ProxyPort = "", $ProxyUser = "", $ProxyPass = "")
    {
        //chrome user agent
        $userAgent = "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/43.0.2357.134 Safari/537.36";
        $headers = array(
            'Connection: Keep-Alive',
            'User-Agent: ' . $userAgent,
        );
    
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        // set header
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        if ($UseProxy) {
            $ProxyUserPass = $ProxyUser . ":" . $ProxyPass;
            curl_setopt($ch, CURLOPT_PROXY, $ProxyURL);
            curl_setopt($ch, CURLOPT_PROXYPORT, $ProxyPort);
            if ($ProxyUser != "" && $ProxyPass != "") {
                curl_setopt($ch, CURLOPT_PROXYUSERPWD, $ProxyUserPass);
                curl_setopt($ch, CURLOPT_PROXYAUTH, CURLAUTH_BASIC|CURLAUTH_ANY);
            }
        }
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

    private function HexToBase64($String)
    {
        $data = hex2bin($String);
        $base64 = base64_encode($data);
        return $base64;
    }

    private function Base64ToHex($String)
    {
        $data = base64_decode($String);
        $hex = bin2hex($data);
        return $hex;
    }

    private function IsValidWidevinePSSH($PSSH)
    {
        $psshHex = $this->Base64ToHex($PSSH);
        $psshHex = strtoupper($psshHex);
        $widevineId = "EDEF8BA979D64ACEA3C827DCD51D21ED";
        $widevineIdPos = strpos($psshHex, $widevineId);
        if ($widevineIdPos !== false) {
            return true;
        } else {
            return false;
        }
    }

    private function ExtractKidFromPSSH($PSSH)
    {
        $psshHex = $this->Base64ToHex($PSSH);
        $psshHex = strtoupper($psshHex);
        $widevineId = "EDEF8BA979D64ACEA3C827DCD51D21ED";
        $widevineIdPos = strpos($psshHex, $widevineId);
        if ($widevineIdPos !== false) {
            $kidPos = $widevineIdPos + strlen($widevineId) + 8;
            $kidSplitter = "1210";
            $kidLength = 32;
            $psshSplit = substr($psshHex, $kidPos);
            $kidPotential = explode($kidSplitter, $psshSplit);
            if (count($kidPotential) > 1) {
                foreach ($kidPotential as $kid) {
                    if (strlen($kid) >= $kidLength) {
                        $kid = strtolower(substr($kid, 0, $kidLength));
                        if (!in_array($kid, $kidArray)) {
                            $kidArray[] = $kid;
                        }
                    }
                }
            }
        }
        return $kidArray;
    }

    public function GetPSSH($PSSH)
    {
        $data = $PSSH;
        // Use regex to extract pssh value
        $pattern = '/<(?:cenc:pssh|pssh)\s*[^>]*>(.*?)<\/(?:cenc:pssh|pssh)>/s';
        $validPssh = '';
        preg_match_all($pattern, $data, $matches);
        foreach ($matches[1] as $pssh) {
            if ($this->IsValidWidevinePSSH($pssh)) {
                return $pssh;
                break;
            }
        }
        return null;
    }

    private function ExtractKidFromManifest($Manifest)
    {
        $data = $Manifest;
        $posDefault = strpos($data, "default_KID");
        $posMarlin = strpos($data, "marlin:kid");
        if ($posDefault !== false) {
            return $this->ExtractCencKid($data);
        } else {
            if ($posMarlin !== false) {
                return $this->ExtractMarlinKid($data);
            } else {
                return [];
            }
        }
    }

    private function ExtractCencKid($Manifest)
    {
        $pattern = '/(?<=cenc:default_KID=")([^"]+)/';
        preg_match_all($pattern, $Manifest, $matches);
        $defaultKids = $matches[1];
        foreach ($defaultKids as $defaultKid) {
            $defaultKid = str_replace("-", "", $defaultKid);
            if (!in_array($defaultKid, $kids)) {
                $kids[] = $defaultKid;
            }
        }
        return $kids;
    }

    private function ExtractMarlinKid($Manifest)
    {
        $pattern = '/<mas:MarlinContentId>([^<]+)/';
        preg_match_all($pattern, $Manifest, $matches);

        $contentIds = $matches[1];
        $kids = [];
        foreach ($contentIds as $contentId) {
            $contentId = str_replace("urn:marlin:kid:", "", $contentId);
            $contentId = ltrim($contentId, ":");
            if (!in_array($contentId, $kids)) {
                $kids[] = $contentId;
            }
        }
        return $kids;
    }

    public function GetKID($Data)
    {
        $URL = $Data["URL"];
        $UseProxy = $Data["UseProxy"] == "true";
        $ProxyURL = $Data["ProxyURL"];
        $ProxyPort = $Data["ProxyPort"];
        $ProxyUser = $Data["ProxyUser"];
        $ProxyPass = $Data["ProxyPass"];

        $data = $this->GetURL($URL, $UseProxy, $ProxyURL, $ProxyPort, $ProxyUser, $ProxyPass);
        $posDefault = strpos($data, "default_KID");
        $posMarlin = strpos($data, "marlin:kid");
        $pssh = $this->GetPSSH($data);
        $kidArray = array();
        // get kid from pssh
        if ($pssh !== null) {
            $kidArray = $this->ExtractKidFromPSSH($pssh);
        }
        // get kid from manifest
        if (count($kidArray) == 0) {
            $kidArray = $this->ExtractKidFromManifest($data);
        }
        // pssh not include any kid
        if (count($kidArray) == 0) {
            if ($posDefault !== false) {
                $kid = substr($data, $posDefault + 13, 36);
                $kid = str_replace("-", "", $kid);
                // check if kid is already in kid array
                if (!in_array($kid, $kidArray)) {
                    $kidArray[] = $kid;
                }
            } elseif ($posMarlin !== false) {
                $kidStart = $posMarlin + 10;
                $kidEnd = strpos($data, "</mas:MarlinContentId>", $kidStart);
                $kid = substr($data, $kidStart, $kidEnd - $kidStart);
                $kid = str_replace("urn:marlin:kid:", "", $kid);
                $kid = ltrim($kid, ":");
                if (!in_array($kid, $kidArray)) {
                    $kidArray[] = $kid;
                }
            } else {
                return null; // Return null if neither "default_KID" nor "marlin:kid" is found
            }
        }

        return $kidArray;
    }

    public function GetUsers()
    {
        $sql = "SELECT * FROM users";
        $st = $this->DB->prepare($sql);
        $st->execute();
        $users = $st->fetchAll(PDO::FETCH_ASSOC);
        return $users;
    }
}
