<?php
$ip = getenv('REMOTE_ADDR');
#$ip = '178.254.55.253';
$bandwidth = $_GET['bw'];
$bandwidthlimit = $_GET['bwl'];
$mountpoints = $_GET['mnt'];
$tstampnow = time();

error_reporting(E_ALL);

if ( ! isset($ip,$bandwidth,$bandwidthlimit,$mountpoints) ) exit;

$maxage = 3600;
$maxagelimitstamp = $tstampnow - $maxage; 

$db = new SQLite3('load.db');
$db-> exec("CREATE TABLE IF NOT EXISTS t_pool(
   target TEXT PRIMARY KEY DEFAULT 'default', 
   machineip TEXT NOT NULL DEFAULT '0.0.0.0',
   bandwidth INTEGER NOT NULL DEFAULT 0,
   bandwidthlimit INTEGER NOT NULL DEFAULT 0, 
  mountpoint TEXT NOT NULL DEFAULT '/unknown',
   timestamp INTEGER NOT NULL DEFAULT $tstampnow)");
$db->exec("DELETE FROM t_pool WHERE timestamp < $maxagelimitstamp");

$a_mountpoints =  preg_split("/[|,]+/",$mountpoints);

foreach ($a_mountpoints as $mountpoint) {
    $target = $ip."_".$mountpoint;
    $db->exec("REPLACE INTO t_pool (
	target,
	machineip,
	bandwidth,
	bandwidthlimit,
	mountpoint,
	timestamp) VALUES (
	'$target',
	'$ip',
	$bandwidth,
	$bandwidthlimit,
	'$mountpoint',
	$tstampnow)");
}; 

$results = $db->query("SELECT * FROM t_pool");

while ( $row = $results->fetchArray() ) {
   echo $row['machineip']."<p>\n";
   echo $row['bandwidth']."<p>\n";
   echo $row['bandwidthlimit']."<p>\n";
   echo $row['mountpoint']."<p>\n";
   echo $row['timestamp']."<p><hr>\n";
}

echo 'OK!<br>'."\n";

?>
