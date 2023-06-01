<?php
ini_set('memory_limit', '-1');
ini_set("log_errors", 1);
ini_set("error_log", "myphp-error.log");
ini_set('display_errors', 1);
set_time_limit(0);
error_reporting(E_ERROR);

include "_db.php";
try {
    $db = new PDO('mysql:host=' . $DBHost . ';dbname=' . $DBName . ';charset=utf8', $DBUser, $DBPass);
} catch (PDOException $e) {
    print "Error!: " . $e->getMessage() . "<br/>";
    die();
}

function ReadConfig()
{
    global $db;
    $sql = "select * from config";
    $st = $db->prepare($sql);
    $st->execute();
    $data = $st->fetchAll();
    return $data;
}
function GetConfig($Config, $ConfigName)
{
    for ($i = 0; $i < count($Config); $i++) {
        if ($Config[$i]["ConfigName"] == $ConfigName) {
            return $Config[$i]["ConfigValue"];
        }
    }
}
function GetChannel($ChID)
{
    global $db;
    $sql = "select ID, ChannelName, Manifest, PID,
  SegmentJoiner, PlaylistLimit, URLListLimit, DownloadUseragent, AudioID, VideoID, Output, UDPIP
  from channels where ID=:ID";
    $st = $db->prepare($sql);
    $st->bindParam(":ID", $ChID);
    $st->execute();
    $line = $st->fetch();
    $line["AudioIDs"] = explode(",", $line["AudioID"]);

    $keySql = "select * from channel_keys where ChannelID=:ID";
    $st = $db->prepare($keySql);
    $st->bindParam(":ID", $ChID);
    $st->execute();
    $line["Keys"] = $st->fetchAll();

    $headersSql = "select Value from channel_headers where ChannelID=:ID";
    $st = $db->prepare($headersSql);
    $st->bindParam(":ID", $ChID);
    $st->execute();
    $line["CustomHeaders"] = $st->fetchAll();

    if ($line["ID"] != "") {
        return $line;
    } else {
        echo "channel id not found";
        die();
    }
}
function UpdateChanStatus($ChID, $Status = "Downloading")
{
    global $db;
    static $RunOnlyOnce;
    if (!$RunOnlyOnce) {
        $RunOnlyOnce = 1;
        $PID = intval(getmypid());
        $sql = "update channels set Status='$Status', StartTime='" . date("Y-m-d H:i:s") . "', PID=$PID where ID=$ChID";
        $db->exec($sql);
    }
}
function UpdateChanStatus2($ChID, $Status = "Error")
{
    global $db;
    $PID = intval(getmypid());
    $sql = "update channels set Status='$Status', StartTime='" . date("Y-m-d H:i:s") . "', PID=$PID where ID=$ChID";
    $db->exec($sql);
}
function UpdateFPID($ChID, $FPID)
{
    global $db;
    $sql = "update channels set FPID=:FPID where ID=:ID";
    $st = $db->prepare($sql);
    $st->bindParam(":FPID", $FPID);
    $st->bindParam(":ID", $ChID);
    $st->execute();
}

function InitiateFolders($ChName, $WorkPath)
{
    mkdir("tmp", 777, true);
    mkdir($WorkPath, 777, true);
    mkdir($WorkPath . "/" . $ChName, 777, true);
    mkdir($WorkPath . "/" . $ChName . "/seg", 777, true);
    mkdir($WorkPath . "/" . $ChName . "/stream", 777, true);
    mkdir($WorkPath . "/" . $ChName . "/ts", 777, true);
    mkdir($WorkPath . "/" . $ChName . "/hls", 777, true);
    mkdir($WorkPath . "/" . $ChName . "/log", 777, true);
    mkdir($WorkPath . "/" . $ChName . "/aria", 777, true);
    if (!file_exists($WorkPath . "/" . $ChName . "/cache")) {
        mkdir($WorkPath . "/" . $ChName . "/cache", 777, true);
    }

    array_map('unlink', array_filter((array) glob("tmp/*")));
    array_map('unlink', array_filter((array) glob($WorkPath . "/" . $ChName . "/seg/*")));
    array_map('unlink', array_filter((array) glob($WorkPath . "/" . $ChName . "/stream/*")));
    array_map('unlink', array_filter((array) glob($WorkPath . "/" . $ChName . "/ts/*")));
    array_map('unlink', array_filter((array) glob($WorkPath . "/" . $ChName . "/log/*")));
    array_map('unlink', array_filter((array) glob($WorkPath . "/" . $ChName . "/hls/*")));
    array_map('unlink', array_filter((array) glob($WorkPath . "/" . $ChName . "/aria/*")));
    array_map('unlink', array_filter((array) glob($WorkPath . "/" . $ChName . "/*")));
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    } else {
        exec("sudo chmod -R 777 " . $WorkPath);
        exec("sudo chown -R www-data:www-data " . $WorkPath);
        exec("sudo chmod -R 777 " . $WorkPath . "/" . $ChName);
        exec("sudo chown -R www-data:www-data " . $WorkPath . "/" . $ChName);
    }
}
function LoadMPD($mpd_url, $UseProxy, $Proxy, $Useragent, $customHeaders = [])
{
    $data = Download($mpd_url, $UseProxy, $Proxy, $Useragent, $customHeaders);
    if ($data) {
        $loaded = simplexml_load_string($data);
        if ($loaded) {
            $dom_sxe1 = dom_import_simplexml($loaded);
            $dom_doc = new DOMDocument('1.0');
            $dom_sxe = $dom_doc->importNode($dom_sxe1, true);
            $dom_doc->appendChild($dom_sxe);
            $main_element_nodes = $dom_doc->getElementsByTagName('MPD');
            return $main_element_nodes->item(0);
        } else {
            return null;
        }
    } else {
        return null;
    }
}
function ParseMPD_XML($mpd_xml)
{
    return extract_features($mpd_xml);
}
function extract_features($xml)
{
    $array = array();
    $attributes = $xml->attributes;
    $children = $xml->childNodes;

    foreach ($attributes as $attribute) {
        $array[$attribute->nodeName] = $attribute->nodeValue;
    }

    foreach ($children as $child) {
        if (is_element($child)) {
            $array[$child->nodeName][] = extract_features($child);
        }

        if ($child->nodeName == 'BaseURL') {
            $array['BaseURL'][sizeof($array['BaseURL']) - 1]['anyURI'] = $child->firstChild->nodeValue;
        }

    }
    return $array;
}
function is_element($element)
{
    if (!empty($element->nodeName) && $element->nodeType == XML_ELEMENT_NODE) {
        return true;
    }

    return false;
}
function current_period($mpd_features, $current_period)
{
    $period_info = period_duration_info($mpd_features);
    $AST = $mpd_features['availabilityStartTime'];

    if ($mpd_features['type'] === 'static') {
        $start = $period_info[0][$current_period];
        $duration = $period_info[1][$current_period];
    } elseif ($mpd_features['type'] === 'dynamic') {
        if (sizeof($mpd_features['Period']) == 1) {
            $current_period = 0;
            $start = $period_info[0][0];
            $duration = $period_info[1][0];
        } else {
            $now = time();
            for ($p = 0; $p < sizeof($mpd_features['Period']); $p++) {
                $whereami = $now - (strtotime($AST) + $period_info[0][$p]);

                if ($whereami <= $period_info[1][$p]) {
                    $current_period = $p;
                    $start = $period_info[0][$p];
                    $duration = $period_info[1][$p];
                    break;
                }
            }
        }
    }
    return [$start, $duration];
}
function period_duration_info($mpd_features)
{
    $periods = $mpd_features['Period'];
    if (array_key_exists('mediaPresentationDuration', $mpd_features)) {
        $mediapresentationduration = time_parsing($mpd_features['mediaPresentationDuration']);
    } else {
        $mediapresentationduration = 0;
    }

    $starts = array();
    $durations = array();
    for ($i = 0; $i < sizeof($periods); $i++) {
        $period = $periods[$i];

        $start = $period['start'];
        $duration = $period['duration'];
        if ($start == '') {
            if ($i > 0) {
                if ($durations[$i - 1] != '') {
                    $start = (float) ($starts[$i - 1] + $durations[$i - 1]);
                } else {
                    if ($mpd_features['type'] == 'dynamic') {
                        //early available period
                    }
                }
            } else {
                if ($mpd_features['type'] == 'static') {
                    $start = 0;
                } elseif ($mpd_features['type'] == 'dynamic') {
                    //early available period
                }
            }
        } else {
            $start = time_parsing($start);
        }

        if ($duration == '') {
            if ($i != sizeof($periods) - 1) {
                $duration = time_parsing($periods[$i + 1]['start']) - $start;
            } else {
                $duration = $mediapresentationduration - $start;
            }
        } else {
            $duration = time_parsing($duration);
        }

        $starts[] = $start;
        $durations[] = min([$duration, 1800]);
    }

    return [$starts, $durations];
}
function time_parsing($var)
{
    $y = str_replace("P", "", $var);
    if (strpos($y, 'Y') !== false) { // Year
        $Y = explode("Y", $y);

        $y = substr($y, strpos($y, 'Y') + 1);
    } else {
        $Y[0] = 0;
    }

    if (strpos($y, 'M') !== false && strpos($y, 'M') < strpos($y, 'T')) { // Month
        $Mo = explode("M", $y);
        $y = substr($y, strpos($y, 'M') + 1);
    } else {
        $Mo[0] = 0;
    }

    if (strpos($y, 'W') !== false) { // Week
        $W = explode("W", $y);
        $y = substr($y, strpos($y, 'W') + 1);
    } else {
        $W[0] = 0;
    }

    if (strpos($y, 'D') !== false) { // Day
        $D = explode("D", $y);
        $y = substr($y, strpos($y, 'D') + 1);
    } else {
        $D[0] = 0;
    }

    $y = str_replace("T", "", $y);
    if (strpos($y, 'H') !== false) { // Hour
        $H = explode("H", $y);
        $y = substr($y, strpos($y, 'H') + 1);
    } else {
        $H[0] = 0;
    }

    if (strpos($y, 'M') !== false) { // Minute
        $M = explode("M", $y);
        $y = substr($y, strpos($y, 'M') + 1);
    } else {
        $M[0] = 0;
    }

    $S = explode("S", $y); // Second

    $duration = (intval($Y[0]) * 365 * 24 * 60 * 60) +
    (intval($Mo[0]) * 30 * 24 * 60 * 60) +
    (intval($W[0]) * 7 * 24 * 60 * 60) +
    (intval($D[0]) * 24 * 60 * 60) +
    (intval($H[0]) * 60 * 60) +
    (intval($M[0]) * 60) +
    intval($S[0]); // calculate durations in seconds

    return $duration;
}
function process_base_url($mpd_url, $mpd_features, $current_period)
{
    $base_url_used = false;
    $mpd_base = $mpd_features['BaseURL'];

    $period = $mpd_features['Period'][$current_period];
    $period_base = $period['BaseURL'];

    $adapts = $period['AdaptationSet'];
    foreach ($adapts as $adapt) {
        $adapt_base = $adapt['BaseURL'];

        $reps = $adapt['Representation'];
        foreach ($reps as $rep) {
            $rep_base = $rep['BaseURL'];

            if ($mpd_base || $period_base || $adapt_base || $rep_base) {
                $base_url_used = true;

                $dir = '';
                $array = array($mpd_base, $period_base, $adapt_base, $rep_base);
                foreach ($array as $item) {
                    if ($item) {
                        $base = $item[0]['anyURI'];
                        if (isAbsoluteURL($base)) {
                            $dir = $base;
                        } else {
                            $dir = $dir . $base;
                        }

                    }

                    $rep_url = $dir;
                }
                if (!isset($rep_url)) {
                    $rep_url = dirname($mpd_url) . '/';
                }

            } else {
                $rep_url = dirname($mpd_url) . '/';
            }

            if (!isAbsoluteURL($rep_url)) {
                $rep_url = dirname($mpd_url) . '/' . $rep_url;
            }

            $rep_urls[] = $rep_url;
        }
        $adapt_urls[] = $rep_urls;
        $rep_urls = array();
    }
    return $adapt_urls;
}
function isAbsoluteURL($URL)
{
    $parsedURL = parse_url($URL);
    return $parsedURL['scheme'] && $parsedURL['host'];
}
function derive_segment_URLs($mpd_features, $current_period, $urls, $period_info)
{
    $period = $mpd_features['Period'][$current_period];
    $adaptation_sets = $period['AdaptationSet'];
    $adapt_segment_urls = array();
    foreach ($adaptation_sets as $i => $adaptation_set) {
        $segment_template_high = get_segment_access($period['SegmentTemplate'], $adaptation_set['SegmentTemplate']);
        $segment_base_high = get_segment_access($period['SegmentBase'], $adaptation_set['SegmentBase']);

        $representations = $adaptation_set['Representation'];
        $segment_access = array();
        $segment_urls = array();
        foreach ($representations as $j => $representation) {

            $segment_template_low = get_segment_access($segment_template_high, $representation['SegmentTemplate']);
            $segment_base_low = get_segment_access($segment_base_high, $representation['SegmentBase']);

            if ($segment_template_low) {
                $segment_access[] = $segment_template_low;
                $segment_info = compute_timing($period_info[1], $segment_template_low[0], 'SegmentTemplate', $urls[$i][$j]);
                $segment_urls[] = compute_URLs($mpd_features, $representation, $i, $j, $segment_template_low[0], $segment_info, $urls[$i][$j]);
            } elseif ($segment_base_low) {
                $segment_access[] = $segment_base_low;
                $segment_urls[] = array($urls[$i][$j]);
            } else {
                $segment_access[] = '';
                $segment_urls[] = array($urls[$i][$j]);
            }
        }
        $adapt_segment_urls[] = $segment_urls;
        $segment_info = array();
    }

    return $adapt_segment_urls;
}
function get_segment_access($high_level, $low_level)
{
    $high_level_exists = !empty($high_level);
    $low_level_exists = !empty($low_level);

    if (!$high_level_exists && !$low_level_exists) {
        return null;
    } elseif ($high_level_exists && !$low_level_exists) {
        return $high_level;
    } elseif (!$high_level_exists && $low_level_exists) {
        return $low_level;
    } else {
        return form_segment_access($high_level, $low_level);
    }

}
function form_segment_access($high, $low)
{
    foreach ($high as $index => $high_i) {
        $low_i = $low[$index];
        foreach ($high_i as $high_key => $high_value) {
            if (!$low_i[$high_key]) {
                $low_i[$high_key] = $high_value;
            } else {
                if (gettype($low_i[$high_key]) == 'array') {
                    $low_i[$high_key] = form_segment_access($high_i[$high_key], $low_i[$high_key]);
                }

            }
        }
        $low[$index] = $low_i;
    }
    return $low;
}
function compute_timing($presentationduration, $segment_access, $segment_access_type, $rep_base_url)
{
    global $MyD;
    $segment_timings = array();
    $segmentno = 0;
    $start = 0;

    switch ($segment_access_type) {
        case 'SegmentTemplate':
            $duration = ($segment_access['duration'] != null) ? $segment_access['duration'] : 0;
            $timescale = ($segment_access['timescale'] != null) ? $segment_access['timescale'] : 1;
            $availabilityTimeOffset = ($segment_access['availabilityTimeOffset'] != null && $segment_access['availabilityTimeOffset'] != 'INF') ? $segment_access['availabilityTimeOffset'] : 0;
            //$availabilityTimeOffset += ($rep_base_url['availabilityTimeOffset']) ? $rep_base_url['availabilityTimeOffset'] : 0;
            $pto = ($segment_access['presentationTimeOffset'] != '') ? (int) ($segment_access['presentationTimeOffset']) / $timescale : 0;

            if ($duration != 0) {
                $duration /= $timescale;
                $segmentno = ceil(($presentationduration - $start) / $duration);
            }

            $segment_timeline = $segment_access['SegmentTimeline'];
            if ($segment_timeline != null) {
                $S_array = $segment_timeline[0]['S'];

                if ($S_array != null) {
                    $segment_time = ($S_array[0]['t']) ? $S_array[0]['t'] : 0;
                    $segment_time -= $pto;
                    $segment_time -= $availabilityTimeOffset;

                    foreach ($S_array as $index => $S) {
                        $d = $S['d'];
                        $MyD[] = ($d / $timescale);

                        if (array_key_exists("r", $S)) {
                            $r = ($S['r']) ? $S['r'] : 0;
                        } else {
                            $r = 0;
                        }
                        if (array_key_exists("t", $S)) {
                            $t = ($S['t']) ? $S['t'] : 0;
                        } else {
                            $t = 0;
                        }
                        $t -= $pto;
                        $t -= $availabilityTimeOffset;

                        if ($r == 0) {
                            $segment_timings[] = number_format((float) $segment_time, 0, '', '');
                            $segment_time += $d;
                        } elseif ($r < 0) {
                            if (!isset($S_array[$index + 1])) {
                                $end_time = $presentationduration * $timescale;
                            } else {
                                $end_time = ($S_array[$index + 1]['t']);
                            }

                            while ($segment_time < $end_time) {
                                $segment_timings[] = number_format((float) $segment_time, 0, '', '');
                                $segment_time += $d;
                            }
                        } else {
                            for ($st = 0; $st <= $r; $st++) {
                                $segment_timings[] = number_format((float) $segment_time, 0, '', '');
                                $segment_time += $d;
                            }
                        }
                    }
                }

                $startnumber = 1;
                $segmentno = sizeof($segment_timings);

            } else {
                $index = 0;
                $segment_time = $start - $pto - $availabilityTimeOffset;
                while ($index < $segmentno) {
                    $segment_timings[] = $segment_time;
                    $segment_time += $duration;
                    $index++;
                }
            }
            break;
        case 'SegmentBase':
            $segment_timings[] = $start;
            $segmentno = 1;
            break;
        default:
            break;
    }

    return [$segment_timings, $segmentno];
}
function compute_URLs($mpd_features, $representation, $adaptation_set_id, $representation_id, $segment_access, $segment_info, $rep_base_url)
{
    $startNumber = ($segment_access['startNumber'] != null) ? $segment_access['startNumber'] : 1;
    $initialization = $segment_access['initialization'];
    $media = $segment_access['media'];
    $bandwidth = $representation['bandwidth'];
    $id = $representation['id'];
    $segment_urls = array();

    if ($initialization != null) {
        $init = str_replace(array('$Bandwidth$', '$RepresentationID$'), array($bandwidth, $id), $initialization);
        if (isAbsoluteURL($init)) {
            $init_url = $init;
        } else {
            if (substr($rep_base_url, -1) == '/') {
                $init_url = $rep_base_url . $init;
            } else {
                $init_url = $rep_base_url . "/" . $init;
            }

        }
        $segment_urls[] = $init_url;
    }

    $index = 0;
    $until = $segment_info[1];

    if ($mpd_features['type'] == 'dynamic') {
        //list($index, $until, $time1) = dynamic_number($adaptation_set_id, $representation_id, $segment_access, $segment_info[0], $segment_info[1]);
    }

    $error_info = '';

    $x = 0;
    while ($index < $until) {
        $segmenturl = str_replace(array('$Bandwidth$', '$Number$', '$RepresentationID$', '$Time$'), array($bandwidth, $index + $startNumber, $id, $segment_info[0][$time1]), $media);
        $x++;
        $pos = strpos($segmenturl, '$Number');
        if ($pos !== false) {
            if (substr($segmenturl, $pos + strlen('$Number'), 1) === '%') {
                $segmenturl = sprintf($segmenturl, $startNumber + $index);
                $segmenturl = str_replace('$Number', '', $segmenturl);
                $segmenturl = str_replace('$', '', $segmenturl);
                $x++;
            } else {
                $error_info = "It cannot happen! the format should be either \$Number$ or \$Number%xd$!";
            }

        }
        $pos = strpos($segmenturl, '$Time');
        if ($pos !== false) {
            if (substr($segmenturl, $pos + strlen('$Time'), 1) === '%') {
                $segmenturl = sprintf($segmenturl, $segment_info[0][$index]);
                $segmenturl = str_replace('$Time', '', $segmenturl);
                $segmenturl = str_replace('$', '', $segmenturl);
            } else {
                $error_info = "It cannot happen! the format should be either \$Time$ or \$Time%xd$!";
            }

        }

        if (!isAbsoluteURL($segmenturl)) {
            if (substr($rep_base_url, -1) == '/') {
                $segmenturl = $rep_base_url . $segmenturl;
            } else {
                $segmenturl = $rep_base_url . "/" . $segmenturl;
            }

        }
        $segment_urls[] = $segmenturl;
        $index++;
        $time1++;
    }

    //if($error_info != '')
    //    error_log($error_info);

    return $segment_urls;
}
function in_Timeline($url, $arr)
{
    foreach ($arr as $item) {
        //if($item["a"] == $url["a"] && $item["v"] == $url["v"]){
        if ($item["v"] == $url["v"]) {
            return true;
        }
    }
    return false;
}
function JoinSegment($ChID, $ChName, $Keys, $aHeader, $aData, $vHeader, $vData, $DownloadIndex)
{
    global $WorkPath, $BinPath;
    global $FFMpegCMD;
    global $DeleteEncryptedAfterDecrypt;
    global $DeleteDecryptedAfterMerge;
    global $PlaylistLimit;
    global $db;
    global $CheckKey;

    $MyFFMpegCMD = $FFMpegCMD;

    $Index = str_pad($DownloadIndex, 8, "0", STR_PAD_LEFT);
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        $Mp4Decrypt = $BinPath . '\\mp4decrypt.exe ';
        $FFMpegBin = $BinPath . '\\ffmpeg.exe ';
        $Redirect = " > nul";
    } else {
        $Mp4Decrypt = $BinPath . '/mp4decrypt ';
        $FFMpegBin = 'ffmpeg ';
        $Redirect = " 2>&1 & ";
    }

    $audio_ext = ".m4a";
    $video_ext = ".mp4";
    $OutExt = ".mp4";
    $OutExt2 = ".ts";

    $Merged_FileName = $WorkPath . "/" . $ChName . "/stream/$Index" . $OutExt2;

    $map = "";
    /** let mp4decrypt bruteforce the key */
    $keyString = "";
    foreach ($Keys as $key) {
        $kid = $key["KID"];
        $decKey = $key['Key'];
        $keyString .= "--key $kid:$decKey ";
    }
    DoLog("Decrypting segment .... please wait .....");
    for ($k = 0; $k < count($aData); $k++) {
        $AudioEncFileName[] = $WorkPath . "/" . $ChName . "/seg/" . $Index . "-" . $k . "-enc" . $audio_ext;
        $AudioDecFileName[] = $WorkPath . "/" . $ChName . "/seg/" . $Index . "-" . $k . "-dec" . $audio_ext;
        $aSeg = null;
        $aSeg = $aHeader . $aData[$k];
        file_put_contents($AudioEncFileName[$k], $aSeg);
        $dec = $Mp4Decrypt . $keyString . $AudioEncFileName[$k] . " " . $AudioDecFileName[$k] . " --show-progress " . $Redirect;
        exec($dec);
        $map .= " -map " . ($k + 1) . ":a ";
    }

    $VideoEncFileName = $WorkPath . "/" . $ChName . "/seg/" . $Index . "-enc" . $video_ext;
    $VideoDecFileName = $WorkPath . "/" . $ChName . "/seg/" . $Index . "-dec" . $video_ext;
    $vSeg = $vHeader . $vData;
    file_put_contents($VideoEncFileName, $vSeg);
    $dec = $Mp4Decrypt . $keyString . $VideoEncFileName . " " . $VideoDecFileName . " --show-progress " . $Redirect;
    exec($dec);

    $MyFFMpegCMD = str_replace("-i", "", $MyFFMpegCMD);
    $MyFFMpegCMD = str_replace("[VIDEO]", " -i " . $VideoDecFileName, $MyFFMpegCMD);
    for ($k = 0; $k < count($aData); $k++) {
        $strAudioIn .= " -copyts -i " . $AudioDecFileName[$k] . " ";
    }
    $MyFFMpegCMD = str_replace("[AUDIO]", $strAudioIn, $MyFFMpegCMD);
    $MyFFMpegCMD = str_replace("[OUTPUT]", $Merged_FileName, $MyFFMpegCMD);
    $cmd = $FFMpegBin . " -copyts " . $MyFFMpegCMD . $Redirect;

    //$cmd=$FFMpegBin." -hide_banner -start_at_zero -correct_ts_overflow 0 -avoid_negative_ts disabled -max_interleave_delta 0 -i $VideoDecFileName $strAudioIn -map 0:v $map -c:v copy -c:a copy $Merged_FileName";
    $cmd = $FFMpegBin . " -hide_banner -probesize 10M -analyzeduration 10M -fflags +igndts -copyts -i $VideoDecFileName $strAudioIn -map 0:v $map -c:v copy -c:a aac -bsf:a aac_adtstoasc $Merged_FileName ";
    echo $cmd;
    $Res = null;
    exec($cmd, $Res);

    if ($CheckKey) {
        $cmd = "ffmpeg -v error -i $Merged_FileName -f null - > $WorkPath/$ChName/log/checkkey.txt 2>&1";
        exec($cmd);
        $Err = file_get_contents("$WorkPath/$ChName/log/checkkey.txt");
        if (strpos($Err, "error while decoding") === false) {
            //ok
        } else {
            UpdateChanStatus2($ChID, "KeyError");
            die();
        }
    }

    $cmd = "ffprobe -v quiet -print_format json -show_streams -show_format $Merged_FileName > a.json";
    exec($cmd);
    $v = json_decode(file_get_contents("a.json"), true);
    unlink("a.json");

    $info["vcodec"] = $v["streams"][0]["codec_name"];
    $info["width"] = $v["streams"][0]["width"];
    $info["height"] = $v["streams"][0]["height"];
    $info["ratio"] = $v["streams"][0]["display_aspect_ratio"];
    $info["framerate"] = $v["streams"][0]["avg_frame_rate"];
    $info["acodec"] = $v["streams"][1]["codec_name"];
    $info["channels"] = $v["streams"][1]["channel_layout"];
    $info["samplerate"] = $v["streams"][1]["sample_rate"];
    $info["bitrate"] = $v["format"]["bit_rate"];
    $data = json_encode($info);
    if ($info["vcodec"]) {
        $sql = "update channels set info='$data' where ID=$ChID";
        $db->exec($sql);
    }

    if ($DeleteEncryptedAfterDecrypt) {
        array_map('unlink', array_filter((array) $AudioEncFileName));
        unlink($VideoEncFileName);
    }
    if ($DeleteDecryptedAfterMerge) {
        array_map('unlink', array_filter((array) $AudioDecFileName));
        unlink($VideoDecFileName);
    }

    UpdatePlaylist($ChName, $ChID, $map);

}

function UpdatePlaylist($ChName, $ChID, $map2)
{
    global $WorkPath, $BinPath;
    global $PlaylistLimit;
    global $FFMpegPID;
    global $db;
    global $Output;
    global $UDPIP;

    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        $FFMpegBin = $BinPath . '\\ffmpeg.exe ';
    } else {
        $FFMpegBin = 'ffmpeg ';
    }
    $OutExt = ".ts";
    $Folder = $WorkPath . "/" . $ChName . "/stream/";
    $Path = $WorkPath . "/" . $ChName . "/stream/*" . $OutExt;
    $Files = glob($Path);
    if (count($Files) > $PlaylistLimit + 3) {
        unlink($Files[0]);
    }
    $Files = glob($Path);
    $seq = 0;

    $m3u8files = "";
    for ($i = 0; $i < count($Files); $i++) {
        $file = str_replace($Folder, "", $Files[$i]);
        if ($seq == 0) {
            $seq = intval(str_replace($OutExt, "", $file));
        }

        $cmd = $FFMpegBin . " -hide_banner -i " . $Files[$i] . " > tmp.txt 2>&1";
        exec($cmd);
        $tmp = file("tmp.txt");
        unlink("tmp.txt");
        foreach ($tmp as $line) {
            if (substr(trim($line), 0, 9) == "Duration:") {
                $dur = explode(":", explode(",", $line)[0]);
            }
        }
        $dur = $dur[count($dur) - 1];
        if ($dur > $MaxDur) {
            $MaxDur = number_format($dur, 2, '.', '');
        }

        $m3u8files .= PHP_EOL . "#EXTINF:$dur," . PHP_EOL . $file;
    }
    $FileHeader = ""
        . "#EXTM3U" . PHP_EOL
        . "#EXT-X-VERSION:3" . PHP_EOL
        . "#EXT-X-MEDIA-SEQUENCE:" . ($seq + 2) . PHP_EOL
        . "#EXT-X-ALLOW-CACHE:YES" . PHP_EOL
        . "#EXT-X-TARGETDURATION:$MaxDur";
    file_put_contents($WorkPath . "/" . $ChName . "/stream/index.m3u8", $FileHeader . $m3u8files);

    if (count($Files) >= $PlaylistLimit - 1) {
        if (!$FFMpegPID || !file_exists('/proc/' . $FFMpegPID)) {
            if (!file_exists('/proc/' . $FFMpegPID) && $FFMpegPID > 0) {
                $Msg = "Channel: " . $ChName . ", ID: " . $ChID . " restarted at: " . date("H:i A");
                $sql = "insert into notification (Title, Msg, Sent, Status) values ('Downloader restart', '$Msg', now(), 'New')";
                $db->exec($sql);
            }
            $cmd = 'ffmpeg ' .
            '-start_at_zero ' .
            '-correct_ts_overflow 0 ' .
            '-avoid_negative_ts disabled ' .
            '-max_interleave_delta 0 ' .
            //'-copyts '.
            '-re ' .
                '-probesize 9000000 ' .
                '-analyzeduration 9000000 ' .
                '-i "' . $WorkPath . '/' . $ChName . '/stream/index.m3u8" ' .
                '-map 0 ' .
                '-vcodec copy ' .
                '-scodec copy ' .
                '-acodec copy ' .
                '-f hls ' .
                '-hls_time 10 ' .
                '-hls_list_size 6 ' .
                '-hls_allow_cache 1 ' .
                '-hls_segment_type mpegts ' .
                '-hls_flags append_list ' .
                '-hls_flags omit_endlist ' .
                '-hls_flags delete_segments ' .
                '-hls_segment_filename "' . $WorkPath . '/' . $ChName . '/hls/seg%06d.ts" ' .
                '"' . $WorkPath . '/' . $ChName . '/hls/index.m3u8" ' .
                '> "' . $WorkPath . '/' . $ChName . '/log/ffmpeg.log" ' .
                '2>&1 & echo $!;';

            $FFMpegPID = exec($cmd, $res);
            UpdateFPID($ChID, $FFMpegPID);
        }
    }
}
function Download($url, $UseProxy = 0, $Proxy = [], $Useragent = "", $customHeaders = [])
{
    $ch = @curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);

    $head[] = "Connection: keep-alive";
    $head[] = "Keep-Alive: 300";
    $head[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
    $head[] = "Accept-Language: en-us,en;q=0.5";

    if ($customHeaders != null && count($customHeaders) > 0) {
        foreach ($customHeaders as $customHeader) {
            $head[] = $customHeader["Value"];
        }
    }

    if ($UseProxy) {
        $ProxyPassUser = $Proxy["User"] . ":" . $Proxy["Pass"];
        curl_setopt($ch, CURLOPT_PROXY, $Proxy["URL"]);
        curl_setopt($ch, CURLOPT_PROXYPORT, $Proxy["Port"]);
        if ($ProxyPassUser != ":") {
            curl_setopt($ch, CURLOPT_PROXYUSERPWD, $ProxyPassUser);
            curl_setopt($ch, CURLOPT_PROXYAUTH, CURLAUTH_ANY);
        }
    }
    if ($Useragent) {
        curl_setopt($ch, CURLOPT_USERAGENT, $Useragent);
    }

    curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $page = curl_exec($ch);
    curl_close($ch);
    return $page;
}
function DownloadRetry($url, $UseProxy = 0, $Proxy = [], $Useragent = "", $customHeaders = [], $maxRetries = 5)
{
    for ($i = 0; $i < $maxRetries; $i++) {
        $page = Download($url, $UseProxy, $Proxy, $Useragent, $customHeaders);
        if ($page) {
            return $page;
        }
        sleep(0.5);
    }
    return null;
}
function DoLog($Msg)
{
    global $WorkPath, $ChName, $ScreenLog, $Config;
    $Msg = date("Y-m-d H:i:s") . " " . $Msg . PHP_EOL;
    if (substr(php_uname(), 0, 7) == "Windows") {
        $LogFileName = $LogPath . "\\" . $ChName . "\\log\\php.log";
    } else {
        $LogFileName = $WorkPath . "/" . $ChName . "/log/php.log";
    }
    $MaxLogSize = intval(GetConfig($Config, "MaxLogSize"));
    if ($MaxLogSize == 0) {
        $MaxLogSize = 1024 * 1024;
    }

    if (filesize($LogFileName) > $MaxLogSize) {
        $handle = fopen($LogFileName, "r");
        $contents = fread($handle, filesize($LogFileName) - $MaxLogSize);
        file_put_contents($LogFileName, $contents);
    }
    file_put_contents($LogFileName, $Msg, FILE_APPEND);
    if ($ScreenLog) {
        echo $Msg;
    }
}

function DownloadList($List, $UseProxy = 0, $Proxy = [], $userAgent = "", $customHeaders = [])
{
    global $WorkPath, $ChName;
    $Folder = $WorkPath . "/" . $ChName;

    $j = (count($List[0]["a"]) + 1) * count($List);

    for ($i = 0; $i < count($List); $i++) {
        for ($k = 0; $k < count($List[$i]["a"]); $k++) {
            $a = $List[$i]["a"][$k];
            $str .= $a . "\r\n out=$i.$k.a\r\n";
        }
        $v = $List[$i]["v"];
        $str .= $v . "\r\n out=$i.v\r\n";
    }
    file_put_contents("list.txt", $str);
    $customHeaderText = "";
    foreach ($customHeaders as $header) {
        $value = $header["Value"];
        $customHeaderText .= "--header=\"$value\" ";
    }
    $userAgentText = "";
    if ($userAgent != "") {
        $userAgentText = "--user-agent=\"$userAgent\" ";
    }
    if ($UseProxy) {
        $cmd = 'aria2c --continue=true -t 10 --check-certificate=false ' . $customHeaderText . ' ' . $userAgentText . '--http-proxy="http://' . $Proxy["User"] . ':' . $Proxy["Pass"] . '@' . $Proxy["URL"] . ':' . $Proxy["Port"] . '" -i list.txt  --dir=' . $Folder . ' -j' . $j;
    } else {
        $cmd = 'aria2c --continue=true -t 10 --check-certificate=false ' . $customHeaderText . ' ' . $userAgentText . '-i list.txt  --dir=' . $Folder . ' -j' . $j;
    }
    echo $cmd;
    exec($cmd);
    for ($i = 0; $i < count($List); $i++) {
        for ($k = 0; $k < count($List[$i]["a"]); $k++) {
            $tmp["a"][$k] .= file_get_contents("$Folder/$i.$k.a");
            exec("sudo rm $Folder/$i.$k.a");
        }
        $tmp["v"] .= file_get_contents("$Folder/$i.v");
        exec("sudo rm $Folder/$i.v");
    }
    return $tmp;
}

$mpd_features;
$current_period = 0;
$current_adaptation_set = 0;
$current_representation = 0;
$period_timing_info = [];
$MyD = [];
$FFMpegPID = null;

$Options = getopt(null, ["mode:", "chid::", "mpdurl::", "proxyurl::", "proxyport::", "proxyuser::", "proxypass::", "useragent::", "screenlog::", "checkkey::"]);

$RunMode = $Options["mode"]; //infoshort, infolong, testonly, download
$CheckKey = $Options["checkkey"];
$ChID = $Options["chid"];
$Mpd_Url = $Options["mpdurl"];

//command line proxy parameters overrides channel and system configurations
$Proxy["URL"] = $Options["proxyurl"];
$Proxy["Port"] = $Options["proxyport"];
$Proxy["User"] = $Options["proxyuser"];
$Proxy["Pass"] = $Options["proxypass"];
$Useragent = $Options["useragent"];
$UseProxy = $Proxy["URL"] != "" ? 1 : 0;
$ScreenLog = $Options["screenlog"];

$Config = ReadConfig();

$DeleteEncryptedAfterDecrypt = GetConfig($Config, "DeleteEncryptedAfterDecrypt");
$DeleteDecryptedAfterMerge = GetConfig($Config, "DeleteDecryptedAfterMerge");
$WorkPath = GetConfig($Config, "DownloadPath");
$BinPath = GetConfig($Config, "BinPath");
$SegmentJoiner = intval(GetConfig($Config, "SegmentJoiner"));
$URLListLimit = intval(GetConfig($Config, "URLListLimit"));
$PlaylistLimit = intval(GetConfig($Config, "PlaylistLimit"));
$FFMpegCMD = GetConfig($Config, "FFMpegCMD");

if ($Useragent == "") {
    $Useragent = GetConfig($Config, "DownloadUseragent");
}
if ($ChID) {
    $ChData = GetChannel($ChID);
    $Output = $ChData["Output"];
    $UDPIP = $ChData["UDPIP"];

    $Mpd_Url = $ChData["Manifest"];

    // $Key                        = $ChData["KID"].":".$ChData["Key"];
    $Keys = $ChData["Keys"];
    $CustomHeaders = $ChData["CustomHeaders"];
    $ChName = str_replace(" ", "_", $ChData["ChannelName"]);

    $Useragent = $ChData["DownloadUseragent"] ? $ChData["DownloadUseragent"] : $Useragent;
    $SegmentJoiner = intval($ChData["SegmentJoiner"]) > 0 ? $ChData["SegmentJoiner"] : $SegmentJoiner;
    $URLListLimit = intval($ChData["URLListLimit"]) >= 1 ? $ChData["URLListLimit"] : $PlaylistLimit;
    $PlaylistLimit = intval($ChData["PlaylistLimit"]) >= 3 ? $ChData["PlaylistLimit"] : $PlaylistLimit;

    if (!isset($Options["proxyurl"])) {
        $UseProxy = intval($ChData["UseProxy"]) == 1;
        if ($UseProxy) {
            if ($ChData["ProxyURL"]) {
                $Proxy["URL"] = $ChData["ProxyURL"];
                $Proxy["Port"] = $ChData["ProxyPort"];
                $Proxy["User"] = $ChData["ProxyUser"];
                $Proxy["Pass"] = $ChData["ProxyPass"];
                $Proxy["Useragent"] = $ChData["ProxyUseragent"];
            } else {
                $Proxy["URL"] = GetConfig($Config, "ProxyURL");
                $Proxy["Port"] = GetConfig($Config, "ProxyPort");
                $Proxy["User"] = GetConfig($Config, "ProxyUser");
                $Proxy["Pass"] = GetConfig($Config, "ProxyPass");
            }
        }
    }
    InitiateFolders($ChName, $WorkPath);
    ini_set("error_log", $WorkPath . "/" . $ChName . "/log/php_error.log");
}
if (!$Mpd_Url) {
    echo "must provide valid mpd url";
    die();
}
$DownloadIndex = 1;
$SegmentCounter = 0;

$aHeader = "";
$vHeader = "";
try {
    Start:
    $Xml_DOM = null;
    $StartTime = time();

    $Xml_DOM = LoadMPD($Mpd_Url, $UseProxy, $Proxy, $Useragent, $CustomHeaders);
    if (!$Xml_DOM) {
        echo "can not load mpd.";
        die();
    }
    $mpd_features = ParseMPD_XML($Xml_DOM);
    $UpdateInterval = time_parsing($mpd_features["minimumUpdatePeriod"]);
    if (intval($UpdateInterval) == 0) {
        $UpdateInterval = array_sum($MyD) / count($MyD);
    }

    if (intval($UpdateInterval) == 0) {
        $UpdateInterval = 2.1;
    }

    $current_period = 0;
    $period_info = current_period($mpd_features, $current_period);
    $period_timing_info = $period_info;

    $urls = process_base_url($Mpd_Url, $mpd_features, $current_period);

    $segment_urls = derive_segment_URLs($mpd_features, $current_period, $urls, $period_info);
    $period = $mpd_features['Period'][$current_period];
    $adaptation_sets = $period['AdaptationSet'];

    switch ($RunMode) {
        case "infoshort":{
                foreach ($adaptation_sets as $Adpt) {
                    $Reprs = $Adpt['Representation'];
                    foreach ($Reprs as $Rep) {
                        if ($Adpt["mimeType"] == "audio/mp4") {
                            $Rep["codecs"] = $Adpt["codecs"];
                            $Rep["lang"] = $Adpt["lang"];
                            $ARep[] = $Rep;
                        }
                        if ($Adpt["mimeType"] == "video/mp4") {
                            $Rep["maxwidth"] = $Adpt["maxWidth"];
                            $Rep["maxheight"] = $Adpt["maxHeight"];
                            $Rep["aframerate"] = $Adpt["frameRate"];
                            $VRep[] = $Rep;
                        }
                    }
                }
                foreach ($ARep as $ar) {
                    foreach ($VRep as $vr) {
                        if (!$vr["width"]) {
                            $vr["width"] = $vr["maxwidth"];
                        }

                        if (!$vr["height"]) {
                            $vr["height"] = $vr["maxheight"];
                        }

                        if (!$vr["frameRate"]) {
                            $vr["frameRate"] = $vr["aframerate"];
                        }

                        echo $ar["lang"] . "|" . $ar["id"] . "|" . $ar["bandwidth"] . "|" . $ar["codecs"] . "|" . $vr["id"] . "|" . $vr["bandwidth"] . "|" . $vr["codecs"] . "|" . $vr["width"] . "|" . $vr["height"] . "|" . $vr["frameRate"] . "\r\n";
                    }
                }
                break;
            }
        case "infojson":{
                foreach ($adaptation_sets as $Adpt) {
                    $Reprs = $Adpt['Representation'];
                    foreach ($Reprs as $Rep) {
                        if ($Adpt["mimeType"] == "audio/mp4") {
                            $Rep["codecs"] = $Adpt["codecs"];
                            $Rep["lang"] = $Adpt["lang"];
                            $ARep[] = $Rep;
                        }
                        if ($Adpt["mimeType"] == "video/mp4") {
                            $Rep["maxwidth"] = $Adpt["maxWidth"];
                            $Rep["maxheight"] = $Adpt["maxHeight"];
                            $Rep["aframerate"] = $Adpt["frameRate"];
                            $VRep[] = $Rep;
                        }
                    }
                }
                foreach ($ARep as $ar) {
                    $Json["a"][] = $ar["id"];
                }
                foreach ($VRep as $vr) {
                    $Json["v"][] = $vr["id"];
                }
                echo json_encode($Json);
                break;
            }
        case "infolong":{
                foreach ($adaptation_sets as $Adpt) {
                    $Reprs = $Adpt['Representation'];
                    foreach ($Reprs as $Rep) {
                        if ($Adpt["mimeType"] == "audio/mp4") {
                            $ARep[] = $Rep;
                        }

                        if ($Adpt["mimeType"] == "video/mp4") {
                            $VRep[] = $Rep;
                        }

                    }
                }
                echo "Audio:\r\n";
                foreach ($ARep as $r) {
                    echo "  id -> " . $r["id"] . str_repeat(" ", 30 - strlen($r["id"])) . " -> bw: " . $r["bandwidth"] . " lang: " . $r["lang"] . "\r\n";
                }
                echo "Video:\r\n";
                foreach ($VRep as $r) {
                    echo "  id -> " . $r["id"] . str_repeat(" ", 30 - strlen($r["id"])) . " -> W: " . $r["width"] . " H: " . $r["height"] . "\r\n";
                }
                break;
            }
        case "testonly":{
                foreach ($adaptation_sets as $Adpt) {
                    $Reprs = $Adpt['Representation'];
                    foreach ($Reprs as $Rep) {
                        if ($Adpt["mimeType"] == "audio/mp4") {$Rep["lang"] = $Adpt["lang"];
                            $ARep[] = $Rep;}
                        if ($Adpt["mimeType"] == "video/mp4") {
                            $VRep[] = $Rep;
                        }

                    }
                }
                echo "Audio:\r\n";
                foreach ($ARep as $r) {
                    echo "  id -> " . $r["id"] . str_repeat(" ", 30 - strlen($r["id"])) . " -> bw: " . $r["bandwidth"] . " lang: " . $r["lang"] . "\r\n";
                }
                echo "Video:\r\n";
                foreach ($VRep as $r) {
                    echo "  id -> " . $r["id"] . str_repeat(" ", 30 - strlen($r["id"])) . " -> W: " . $r["width"] . " H: " . $r["height"] . "\r\n";
                }
                $a_url = [];
                $v_url = [];
                $current_representation = 0;
                $current_adaptation_set = 0;
                while ($current_adaptation_set < sizeof($adaptation_sets)) {
                    $adaptation_set = $adaptation_sets[$current_adaptation_set];
                    $representations = $adaptation_set['Representation'];
                    while ($current_representation < sizeof($representations)) {
                        $representation = $representations[$current_representation];
                        $segment_url = $segment_urls[$current_adaptation_set][$current_representation];
                        $a_url = $segment_urls[$current_adaptation_set][$current_representation];
                        $v_url = $segment_urls[$current_adaptation_set][$current_representation];
                        break (2);
                    }
                }
                echo "\r\n";
                echo "Audio TS Timeline: " . (count($a_url) - 1) . " segments\r\n";
                echo "Video TS Timeline: " . (count($v_url) - 1) . " segments\r\n";
                echo " Segments Average: " . number_format(array_sum($MyD) / count($MyD), 2, '.', '') . "/Seconds\r\n";
                echo "  Update Interval: " . number_format($UpdateInterval, 2, '.', '') . "/Seconds\r\n";
                break;
            }
        case "download":{
                DoLog("---------------- Round loggin start point ----------------");
                DoLog("Downloading in progress .. update channel status");
                UpdateChanStatus($ChID, "Downloading");

                $a_url = [];
                $v_url = [];
                $current_representation = 0;
                $current_adaptation_set = 0;
                while ($current_adaptation_set < sizeof($adaptation_sets)) {
                    $adaptation_set = $adaptation_sets[$current_adaptation_set];
                    $representations = $adaptation_set['Representation'];
                    while ($current_representation < sizeof($representations)) {
                        $representation = $representations[$current_representation];
                        $segment_url = $segment_urls[$current_adaptation_set][$current_representation];
                        if (in_array($representation["id"], $ChData["AudioIDs"])) {
                            $a_url[] = $segment_urls[$current_adaptation_set][$current_representation];
                        }
                        if ($ChData["VideoID"] == $representation["id"]) {
                            $v_url = $segment_urls[$current_adaptation_set][$current_representation];
                        }
                        $current_representation++;
                    }
                    $current_representation = 0;
                    $current_adaptation_set++;
                }

                $aHeaderUrl = $a_url[0][0];
                $vHeaderUrl = $v_url[0];

                for ($k = 0; $k < count($a_url); $k++) {
                    array_shift($a_url[$k]);
                    array_shift($a_url[$k]);
                }
                array_shift($v_url);
                array_shift($v_url);

                $Start = 0;
                $End = count($v_url);

                if ($End > $URLListLimit) {
                    $Start = $End - $URLListLimit;
                }

                $TimelineFile = $WorkPath . "/" . $ChName . "/timeline.json";

                $Timeline = [];
                $ToDownload = 0;
                if (file_exists($TimelineFile)) {
                    $Timeline = json_decode(file_get_contents($TimelineFile), true);
                    if (count($Timeline) > 50) {
                        array_shift($Timeline);
                    }
                }

                $aHeaderFile = $WorkPath . "/" . $ChName . "/cache/ainit_" . md5($aHeaderUrl) . ".mp4";
                $vHeaderFile = $WorkPath . "/" . $ChName . "/cache/vinit_" . md5($vHeaderUrl) . ".mp4";

                if ($aHeader == "") {
                    if (!file_exists($aHeaderFile)) {
                        DoLog("Downloading audio header: " . $aHeaderUrl);
                        $aHeader = DownloadRetry($aHeaderUrl, $UseProxy, $Proxy, $Useragent, $CustomHeaders);
                        if ($aHeader && !file_exists($aHeaderFile)) {
                            file_put_contents($aHeaderFile, $aHeader);
                        }
                    } else {
                        $aHeader = file_get_contents($aHeaderFile);
                    }
                }
                if ($vHeader == "") {
                    if (!file_exists($vHeaderFile)) {
                        DoLog("Downloading video header: " . $vHeaderUrl);
                        $vHeader = DownloadRetry($vHeaderUrl, $UseProxy, $Proxy, $Useragent, $CustomHeaders);
                        if ($vHeader && !file_exists($vHeaderFile)) {
                            file_put_contents($vHeaderFile, $vHeader);
                        }
                    } else {
                        $vHeader = file_get_contents($vHeaderFile);
                    }
                }

                DoLog("Determining timeline start/end");
                for ($i = $Start; $i < $End; $i++) {
                    if ($v_url[$i] != "") {
                        $TimelineItem["v"] = $v_url[$i];
                        $TimelineItem["s"] = 0;
                        $TimelineItem["k"] = md5(microtime(true));
                        for ($k = 0; $k < count($a_url); $k++) {
                            $TimelineItem["a"][$k] = $a_url[$k][$i];
                        }
                        if (!in_Timeline($TimelineItem, $Timeline)) {
                            $Timeline[] = $TimelineItem;
                        }
                    }
                }

                DoLog("Calculating download queue");
                $DTimeline = [];
                for ($i = 0; $i < count($Timeline); $i++) {
                    if ($Timeline[$i]["s"] == 0) {
                        $DTimeline[] = $Timeline[$i];
                        if (count($DTimeline) == $SegmentJoiner) {
                            break;
                        }
                    }
                }
                if (count($DTimeline) == $SegmentJoiner) {
                    DoLog("Process download queue");
                    $aData = [];
                    $vData = null;
                    /*
                    $tmp=DownloadList($DTimeline, $UseProxy, $Proxy);
                    for($k=0;$k<count($tmp["a"]);$k++){
                    $aData[$k].=$tmp["a"][$k];
                    }
                    $vData.=$tmp["v"];
                    $SegCounter+=count($DTimeline);
                     */

                    for ($i = 0; $i < count($DTimeline); $i++) {
                        for ($k = 0; $k < count($DTimeline[$i]["a"]); $k++) {
                            DoLog("   Downloading audio segment: " . $DTimeline[$i]["a"][$k]);
                            $aData[$k] .= DownloadRetry($DTimeline[$i]["a"][$k], $UseProxy, $Proxy, $Useragent, $CustomHeaders);
                        }
                        DoLog("   Downloading video segment: " . $DTimeline[$i]["v"]);
                        $vData .= DownloadRetry($DTimeline[$i]["v"], $UseProxy, $Proxy, $Useragent, $CustomHeaders);
                        $SegmentCounter++;
                        DoLog("   Segments: $SegmentCounter / $SegmentJoiner done");
                        $SegCounter++;
                    }

                    DoLog("Finalizing segment");
                    JoinSegment($ChID, $ChName, $Keys, $aHeader, $aData, $vHeader, $vData, $DownloadIndex);
                    $aData = "";
                    $vData = "";
                    $SegmentCounter = 0;
                    $DownloadIndex++;

                    DoLog("Bookmark timeline");
                    for ($i = 0; $i < count($Timeline); $i++) {
                        for ($j = 0; $j < count($DTimeline); $j++) {
                            if ($Timeline[$i]["k"] == $DTimeline[$j]["k"]) {
                                $Timeline[$i]["s"] = 1;
                            }
                        }
                    }
                } else {
                    DoLog("Waiting for $SegmentJoiner segments in timeline queue");
                }
                for ($i = 0; $i < count($Timeline); $i++) {
                    if ($Timeline[$i]["s"] == 0) {
                        $ToDownload = $ToDownload + 1;
                    }

                }
                file_put_contents($TimelineFile, json_encode($Timeline));
                $EndTime = time();
                $WaitTime = ($UpdateInterval) - (($EndTime - $StartTime));
                DoLog("Running round finished ..");
                DoLog("        Start Time: " . date("Y-m-d H:i:s", $StartTime));
                DoLog("          End Time: " . date("Y-m-d H:i:s", $EndTime));
                DoLog("   Update Interval: " . $UpdateInterval . " seconds");
                DoLog("Status:");
                DoLog("          Timeline: " . $ToDownload . "/$SegmentJoiner");
                DoLog("              Done: " . $SegCounter);
                DoLog("           Waiting: " . $WaitTime . " seconds");
                if ($WaitTime > 0) {
                    sleep($WaitTime);
                }
                goto Start;
            }
    }
} catch (Exception $e) {
    DoLog($e->getMessage());
}