<?php
$ip = getenv('REMOTE_ADDR');
$agent = getenv('HTTP_USER_AGENT');
$mountpoint = $_GET['mnt'];
include("functions.php");
init_db();

$tstampnow = time();

# static switches
switch ($mountpoint) {
    case 'bbr_master.mp3':
	header("Location: http://141.16.141.4/bbr_master.mp3");
        exit;
    case 'tdy_master.mp3':
	header("Location: http://141.16.141.4/tdy_master.mp3");
        exit;
    case 'bbr_cable.mp3':
	header("Location: http://141.16.141.4/bbr_cable.mp3");
        exit;
    case 'rt_cable.mp3':
	header("Location: http://141.16.141.4/rt_cable.mp3");
        exit;
    case 'rt_bremen.mp3':
	header("Location: http://141.16.141.3/rt_bremen.mp3");
        exit;
    case 'rt_kassel.mp3':
	header("Location: http://141.16.141.3/rt_kassel.mp3");
        exit;
    case 'rt_koblenz.mp3':
	header("Location: http://141.16.141.3/rt_koblenz.mp3");
        exit;
    case 'rt_schwerin.mp3':
	header("Location: http://141.16.141.3/rt_schwerin.mp3");
        exit;
# very special | rewrite some stuff
    case 'egal.mp3':
	header("Location: http://".getenv('HTTP_HOST')."/bbradio.mp3");
        exit;
}

# within which time in seconds clients will be assumed as alive and selectable
$tmaxage = 10;
$tstampoldest = $tstampnow - $tmaxage;

error_reporting(E_ALL);

# maximum accepted load of a player
$maxload = 75;

# within which time in seconds clients will stay in pool
#$maxage = 3600;
$maxage = 300;
$maxagelimitstamp = $tstampnow - $maxage; 
cleanup_listeners($maxagelimitstamp);

if ( ! isset($ip,$agent,$mountpoint) ) exit;

$fingerprint = md5($ip.$agent);

#$maxage_othermounts = 600;
$maxage_othermounts = 300;
$maxagelimitstamp_othermounts = $tstampnow - $maxage_othermounts; 
cleanup_listeners_othermounts($maxagelimitstamp_othermounts,$fingerprint,$mountpoint);

$db = new SQLite3('load.db');
$db->busyTimeout(3000);
$result = $db->query("SELECT * FROM t_pool WHERE mountpoint LIKE '/$mountpoint' AND ( loadlimit = 0 OR load < loadlimit ) AND timestamp > $tstampoldest AND ( bandwidthlimit = 0 OR bandwidth < bandwidthlimit ) ORDER BY bandwidth ASC LIMIT 1");
#echo $db->lastErrorMsg();

$wellknown = $db->query("SELECT * FROM t_listeners WHERE fingerprint = '$fingerprint' and mountpoint = '$mountpoint'");

$errortext = "this stream is not available";
$errorspeech = urlencode($errortext);

$redirect = "Location: http://translate.google.com/translate_tts?tl=en&q=".$errorspeech;
$prefix = "/intro.";
$playerport = "8000";

while ( $wrow = $wellknown->fetchArray() ) {
    $prefix = "/";
}
while ( $row = $result->fetchArray() ) {
    $mount = ltrim($row['mountpoint'],'/');
    $redirect = "Location: http://".$row['machineip'].":".$playerport.$prefix.$mount;
}

$db->exec("REPLACE INTO t_listeners (
	fingerprint,
	mountpoint,
	timestamp) VALUES (
	'$fingerprint',
	'$mountpoint',
	$tstampnow)");

$db->close();

header($redirect);

?>

