<?php
$ip = getenv('REMOTE_ADDR');
$mountpoint = $_GET['mnt'];
$tstampnow = time();
$tmaxage = 10;
$tstampoldest = $tstampnow - $tmaxage;

error_reporting(E_ALL);

#if ( ! isset($ip,$mountpoint) ) exit;

#$db = new SQLite3('load.db');
#$db-> exec("CREATE TABLE IF NOT EXISTS t_pool(
#   target TEXT PRIMARY KEY DEFAULT 'default', 
#   machineip TEXT NOT NULL DEFAULT '0.0.0.0',
#   bandwidth INTEGER NOT NULL DEFAULT 0,
#   bandwidthlimit INTEGER NOT NULL DEFAULT 0, 
#   load INTEGER NOT NULL DEFAULT 0, 
#   mountpoint TEXT NOT NULL DEFAULT '/unknown',
#   timestamp INTEGER NOT NULL DEFAULT $tstampnow)");

#$result = $db->query("SELECT * FROM t_pool WHERE mountpoint LIKE '$mountpoint' AND timestamp > $tstampoldest ORDER BY bandwidth ASC LIMIT 1");
#$db->close();

#$errortext = "this stream is not available";
#$errorspeech = urlencode($errortext);

#if (empty(array_filter($result))) {
#    $redirect = "Location: http://translate.google.com/translate_tts?tl=en&q=".$errorspeech;
#}

#while ( $row = $result->fetchArray() ) {
#    $redirect = "Location: http://".$row['machineip']."/".$row['mountpoint'];
#}

#header($redirect);
#?>


<?php
$file = "/var/www/html/test.mp3";
header("Refresh: 4;url=http://translate.google.com/translate_tts?tl=en&q=".$errorspeech);
header("Content-Type:audio/mpeg");
header("Content-Transfer-Encoding: binary");
header("Content-Length:".filesize($file));
readfile($file);
?>

#<?php
#$errortext = "this stream is not available";
#$errorspeech = urlencode($errortext);
#$redirect = "Location: http://translate.google.com/translate_tts?tl=en&q=".$errorspeech;
#header($redirect);
#?>
