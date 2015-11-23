<?php
$ip = getenv('REMOTE_ADDR');
$agent = getenv('HTTP_USER_AGENT');
$mountpoint = $_GET['mnt'];
include("functions.php");
init_db();

$tstampnow = time();
$tmaxage = 10;
$tstampoldest = $tstampnow - $tmaxage;

error_reporting(E_ALL);

$maxage = 3600;
$maxagelimitstamp = $tstampnow - $maxage; 
cleanup_listeners($maxagelimitstamp);

if ( ! isset($ip,$agent,$mountpoint) ) exit;

$fingerprint = md5($ip.$agent.$mountpoint);

$db = new SQLite3('load.db');
$result = $db->query("SELECT * FROM t_pool WHERE mountpoint LIKE '/$mountpoint' AND timestamp > $tstampoldest ORDER BY bandwidth ASC LIMIT 1");
#echo $db->lastErrorMsg();

$wellknown = $db->query("SELECT * FROM t_listeners WHERE fingerprint = '$fingerprint'");

$errortext = "this stream is not available";
$errorspeech = urlencode($errortext);

$redirect = "Location: http://translate.google.com/translate_tts?tl=en&q=".$errorspeech;
$prefix = "/intro.";

while ( $wrow = $wellknown->fetchArray() ) {
    $prefix = "/";
}
while ( $row = $result->fetchArray() ) {
    $mount = ltrim($row['mountpoint'],'/');
    $redirect = "Location: http://".$row['machineip'].$prefix.$mount;
}

$db->exec("REPLACE INTO t_listeners (
	fingerprint,
	timestamp) VALUES (
	'$fingerprint',
	$tstampnow)");

$db->close();

header($redirect);

?>

