<?php
$mountpoint = $_GET['mnt'];
include("functions.php");

init_db();

error_reporting(E_ALL);

$db = new SQLite3('load.db');
$results = $db->query("SELECT machineip,bandwidth,bandwidthlimit,load,timestamp FROM t_pool group by machineip");

echo "<table align=right>";
echo "<tr><th>TSTAMP</th><th>IP</th><th>BW</th><th>BWLIMIT</th><th>LOAD</th></tr>\n";
while ( $row = $results->fetchArray() ) {
    echo "<tr>";
    echo "<td>".$row['timestamp']."</td>";
    echo "<td>".$row['machineip']."</td>";
    echo "<td>".$row['bandwidth']."</td>";
    echo "<td>".$row['bandwidthlimit']."</td>";
    echo "<td>".$row['load']."</td>\n";
    echo "</tr>";
}
echo "</table>";
$db->close();
?>
