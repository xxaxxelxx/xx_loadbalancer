<?php
$mountpoint = $_GET['mnt'];
include("functions.php");

init_db();

error_reporting(E_ALL);

$db = new SQLite3('load.db');
$db->busyTimeout(1000);
$results = $db->query("SELECT * FROM t_pool where mountpoint like '%$mountpoint%'");

while ( $row = $results->fetchArray() ) {
   echo $row['machineip']."&nbsp;|&nbsp;\n";
   echo $row['bandwidth']."&nbsp;|&nbsp;\n";
   echo $row['bandwidthlimit']."&nbsp;|&nbsp;\n";
   echo $row['listeners']."&nbsp;|&nbsp;\n";
   echo $row['mountpoint']."&nbsp;|&nbsp;\n";
   echo $row['load']."&nbsp;|&nbsp;\n";
   echo $row['timestamp']."<p><hr>\n";
}
$db->close();
echo "<hr>OK!<br>\n";

?>
