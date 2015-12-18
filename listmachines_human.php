<?php
$mountpoint = $_GET['mnt'];
include("functions.php");

init_db();

error_reporting(E_ALL);

$db = new SQLite3('load.db');
$results = $db->query("SELECT machineip,bandwidth,bandwidthlimit,load,timestamp FROM t_pool group by machineip");
$results2 = $db->query("SELECT mountpoint FROM t_pool group by mountpoint order by mountpoint");

header("refresh: 1;");

echo "<table align=center border=1>";
echo "<tr><th align=right>TSTAMP</th><th align=right>IP</th><th align=right>BW</th><th align=right>BWLIMIT</th><th align=right>LOAD</th>";
    while ( $row2 = $results2->fetchArray() ) {
	echo "<th align=right>".$row['mountpoint']."</th>";
    }
echo "</tr>\n";

while ( $row = $results->fetchArray() ) {
    echo "<tr>";
    echo "<td align=right>".$row['timestamp']."</td>";
    echo "<td align=right>".$row['machineip']."</td>";
    echo "<td align=right>".$row['bandwidth']."</td>";
    echo "<td align=right>".$row['bandwidthlimit']."</td>";
    echo "<td align=right>".$row['load']."</td>\n";
    echo "</tr>";
}
echo "</table>";
$db->close();
?>
