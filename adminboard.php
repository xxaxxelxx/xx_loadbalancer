<?php
$mountpoint = $_GET['mnt'];
include("functions.php");

init_db();

error_reporting(E_ALL);

$db = new SQLite3('load.db');
$results = $db->query("SELECT machineip,bandwidth,bandwidthlimit,load,timestamp FROM t_pool group by machineip");
$results2 = $db->query("SELECT mountpoint FROM t_pool where mountpoint not like \"/intro.%\" group by mountpoint order by mountpoint");

header("refresh: 1;");
echo "<style>";
include 'adminboard.css';
echo "</style>";

echo "<table borderx=1>";
echo "<tr><th class=rotatex><div><span>TSTAMP</th><th class=rotatex><div><span>IP</th><th class=rotatex><div><span>BW</th><th class=rotatex><div><span>BWLIMIT</th><th class=rotatex><div><span>LOAD</th>";
    while ( $row2 = $results2->fetchArray() ) {
    echo "<th class=rotate><div><span>".$row2['mountpoint']."</span></div></th>";
    }
echo "</tr>\n";

$LINESTYLE = '';
while ( $row = $results->fetchArray() ) {
    if ( $LINESTYLE == 'A' ) { $LINESTYLE = 'B'; } else { $LINESTYLE = 'A';};
    echo "<tr>";
    echo "<td align=right><div class=$LINESTYLE><span>".$row['timestamp']."</span></div></td>";
    echo "<td align=right><div class=$LINESTYLE><span>".$row['machineip']."</span></div></td>";
    echo "<td align=right><div class=$LINESTYLE><span>".$row['bandwidth']."</span></div></td>";
    echo "<td align=right><div class=$LINESTYLE><span>".$row['bandwidthlimit']."</span></div></td>";
    echo "<td align=right><div class=$LINESTYLE><span>".$row['load']."</span></div></td>\n";

    while ( $row2 = $results2->fetchArray() ) {
    $mountpoint = ltrim ($row2['mountpoint'], '/');
    $results3 = $db->query("SELECT listeners FROM t_pool where mountpoint like \"%".$mountpoint."\" and machineip like \"".$row['machineip']."\" ");
    
    $listeners = '';
    while ( $row3 = $results3->fetchArray() ) {
        $listeners = $row3['listeners'];
    }
    if ( $listeners == '' ) {
        echo "<td align=right><div class=$LINESTYLE><span>&nbsp;</span></div></th>";
    } else {
        $intromounts = $db->query("SELECT listeners FROM t_pool where machineip like \"".$row['machineip']."\" and mountpoint like '/intro.$mountpoint'");
        $numberof_intromounts = 0; while ( $row4 = $intromounts->fetchArray() ) { $numberof_intromounts++; };
        $printlisteners = $listeners - $numberof_intromounts;
        echo "<td align=right><div class=$LINESTYLE><span style='background-color: green;'">".$printlisteners."</span></div></th>";
    }
    }
    echo "</tr>";
}
echo "</table>";
$db->close();
?>