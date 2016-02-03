<?php
$ip = getenv('REMOTE_ADDR');
$bandwidth = $_GET['bw'];
$bandwidthlimit = $_GET['bwl'];
$mountpoints = $_GET['mnt'];
$load = $_GET['load'];
$loadlimit = $_GET['loadl'];
$tstampnow = time();

include("functions.php");

init_db();

error_reporting(E_ALL);

if ( ! isset($loadlimit) ) { $loadlimit = 77; };

if ( ! isset($ip,$bandwidth,$bandwidthlimit,$mountpoints,$load,$loadlimit) ) exit;

$maxage = 3600;
$maxagelimitstamp = $tstampnow - $maxage; 
cleanup_pool($maxagelimitstamp);

$a_mountpoints =  preg_split("/[|,]+/",$mountpoints);
$listeners = 99;

$db = new SQLite3('load.db');
$db->busyTimeout(3000);

foreach ($a_mountpoints as $mountpoint) {
    $a_listeners_per_mountpoint = preg_split("/[@]+/",$mountpoint);
    $listeners = $a_listeners_per_mountpoint[0];
    $mnt = $a_listeners_per_mountpoint[1];
    $target = $ip."_".$mnt;
    $db->exec("REPLACE INTO t_pool (
	target,
	machineip,
	bandwidth,
	bandwidthlimit,
	listeners,
	mountpoint,
	load,
	loadlimit,
	timestamp) VALUES (
	'$target',
	'$ip',
	$bandwidth,
	$bandwidthlimit,
	$listeners,
	'$mnt',
	$load,
	$loadlimit,
	$tstampnow)");
}; 
$db->close();

#echo $db->lastErrorMsg();
?>
