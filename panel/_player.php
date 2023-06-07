<?php
include "_config.php";

$ID = $_GET["id"];
if (!is_numeric($ID)) {
    // Invalid input, handle the error (e.g., show an error message or redirect)
    die("Invalid ID");
}

$ID = intval($ID);

$videoUrl = $App->GetConfig("M3UDownloadURL") . "/" . $App->GetChannel($ID)["ChannelName"] . "/hls/index.m3u8";
$videoUrl = str_replace(" ", "_", $videoUrl);
?>
<html>

<head>
    <title>DRMPHP - Player - <?php
echo $App->GetChannel($ID)["ChannelName"];
?></title>
    <style>html { overflow: hidden; }</style>
    <link href="assets/libs/videojs/videojs.min.css" rel="stylesheet">
</head>

<body>
    <video-js id="video" preload="auto" style="width: 100%; height:100%" width="100%" height="100%" class="vjs-default-skin" controls>
        <source src="<?php echo $videoUrl; ?>" type="application/x-mpegURL">
    </video-js>
    <script src="assets/libs/jquery/jquery.min.js"></script>
    <script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/libs/videojs/core.min.js"></script>
    <script src="assets/libs/videojs/http-streaming.min.js"></script>
    <script>
        var player = videojs('video');
        player.play();
    </script>
</body>

</html>