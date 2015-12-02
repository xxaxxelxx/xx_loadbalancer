<?php
$mountpoint = $_GET['mnt'];
include("functions.php");

init_db();

error_reporting(E_ALL);

$db = new SQLite3('load.db');
$results = $db->query("SELECT machineip,bandwidth,bandwidthlimit,load FROM t_pool group by machineip");

while ( $row = $results->fetchArray() ) {
   echo $row['machineip']."|";
   echo $row['bandwidth']."|";
   echo $row['bandwidthlimit']."|";
   echo $row['load']."\n";
}
$db->close();
?>
