<?php
$mountpoint = $_GET['mnt'];
include("functions.php");

init_db();

error_reporting(E_ALL);

$db = new SQLite3('load.db');
$results = $db->query("SELECT machineip,bandwidth,bandwidthlimit,load FROM t_pool group by machineip");

while ( $row = $results->fetchArray() ) {
   echo "1.2.3.4"."|";
   echo "64"."|";
   echo "0"."|";
   echo "99"."\n";
   echo "1.2.3.5"."|";
   echo "128"."|";
   echo "0"."|";
   echo "88"."\n";
}
$db->close();
?>
