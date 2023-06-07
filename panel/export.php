<?php
include "_config.php";

include "_db.php";
try {
    $db = new PDO('mysql:host=' . $DBHost . ';dbname=' . $DBName . ';charset=utf8', $DBUser, $DBPass);
} catch (PDOException $e) {
    print "Error!: " . $e->getMessage() . "<br/>";
    die();
}

// Check if the username and password are provided
if (!isset($_POST['username']) || !isset($_POST['password']) || !isset($_POST['type'])) {
    header('HTTP/1.0 401 Unauthorized');
    echo 'Username, password, and type are required.';
    exit;
}

// Get the username and password from the POST data
$username = $_POST['username'];
$password = $_POST['password'];
$Type = $_POST['type'];

// Prepare and execute a SQL query to fetch user credentials and expiration date
$query = "SELECT * FROM `lines` WHERE `username` = :username";
$stmt = $db->prepare($query);
$stmt->bindValue(':username', $username);

try {
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    header('HTTP/1.0 500 Internal Server Error');
    echo 'Error fetching user credentials.';
    exit;
}

// Verify the user credentials and expiration date
if (!$user || $password !== $user['password']) {
    header('HTTP/1.0 401 Unauthorized');
    echo 'Invalid username or password.';
    exit;
}

$expireDate = strtotime($user['expire_date']);
$currentDate = time();

if ($expireDate < $currentDate) {
    header('HTTP/1.0 403 Forbidden');
    echo 'Your account has expired. Please renew your subscription.';
    exit;
}

if ($Type == "hls") {
    // Assuming you have the necessary logic to generate the $m3u8 content
    // Replace the logic below with your actual implementation
    $m3u8 = generateM3U8Content();

    header("Content-type: application/x-mpegURL");
    header("Content-Disposition: attachment; filename=list.m3u8");
    echo $m3u8;
} else {
    header('HTTP/1.0 400 Bad Request');
    echo 'Invalid export type.';
    exit;
}

// Function to generate the M3U8 content
function generateM3U8Content()
{
    global $App;
    $Data = $App->GetAllChannels();
    $DownloadURL = $App->GetConfig("M3UDownloadURL");
    $m3u8files = '';

    for ($i = 0; $i < count($Data); $i++) {
        if ($Data[$i]["Status"] == "Downloading") {
            $ChName = str_replace(" ", "_", $Data[$i]["ChannelName"]);
            $URL = $DownloadURL . "/" . $ChName . "/hls/index.m3u8";
            $m3u8files .= "#EXTINF:-1, " . $Data[$i]["ChannelName"] . PHP_EOL . $URL . PHP_EOL;
        }
    }

    if ($m3u8files) {
        $m3u8files = rtrim($m3u8files, PHP_EOL);
    }

    $m3u8 = "#EXTM3U" . PHP_EOL . $m3u8files . PHP_EOL . "#EXT-X-ENDLIST";
    return $m3u8;
}
