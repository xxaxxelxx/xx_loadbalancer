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
include 'tacticsboard.css';
echo "</style>";
echo "<div><span class=headline>TACTICS</span></div>";
echo "<div><span class=tstamp>".date(DATE_RFC822)."</span></div>";
echo "<hr><br>";

$results7 = $db->query("SELECT sum(listeners) FROM t_pool");
while ( $row7 = $results7->fetchArray() ) {
    $listenersumsum = $row7[0];
}
$intromounts7 = $db->query("SELECT listeners FROM t_pool where mountpoint like '/intro%'");
$numberof_intromounts7 = 0; while ( $row8 = $intromounts7->fetchArray() ) { $numberof_intromounts7++; };
$printlistenersumsum = $listenersumsum - $numberof_intromounts7;
echo "<div><span class=sumsum>".$printlistenersumsum."</span></div>";
echo "<hr><br>";

echo "<table borderx=1>";
echo "<tr><th class=rotatex><div><span>TSTAMP</span></div></th><th class=rotatex><div><span>IP</span></div></th><th class=rotatex><div><span>BW</span></div></th><th class=rotatex><div><span>BWLIMIT</span></div></th><th class=rotatex><div><span>LOAD</span></div></th><th class=rotatex><div><span>LISTENERS</span></div></th>";
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
    $results5 = $db->query("SELECT sum(listeners) FROM t_pool where machineip like \"".$row['machineip']."\" ");
    while ( $row5 = $results5->fetchArray() ) {
        $listenersum = $row5[0];
    }
    $intromounts2 = $db->query("SELECT listeners FROM t_pool where machineip like \"".$row['machineip']."\" and mountpoint like '/intro%'");
    $numberof_intromounts2 = 0; while ( $row6 = $intromounts2->fetchArray() ) { $numberof_intromounts2++; };
    $printlistenersum = $listenersum - $numberof_intromounts2;

    echo "<td align=right><div class=$LINESTYLE><span>".$printlistenersum."</span></div></td>\n";

    while ( $row2 = $results2->fetchArray() ) {
    $mountpoint = ltrim ($row2['mountpoint'], '/');
    $results3 = $db->query("SELECT sum(listeners) FROM t_pool where mountpoint like \"%".$mountpoint."\" and machineip like \"".$row['machineip']."\" ");
    
    $listeners = '';
    while ( $row3 = $results3->fetchArray() ) {
        $listeners = $row3[0];
    }
    if ( $listeners == '' ) {
        echo "<td align=right><div class=$LINESTYLE><span>&nbsp;</span></div></th>";
    } else {
        $intromounts = $db->query("SELECT listeners FROM t_pool where machineip like \"".$row['machineip']."\" and mountpoint like '/intro.$mountpoint'");
        $numberof_intromounts = 0; while ( $row4 = $intromounts->fetchArray() ) { $numberof_intromounts++; };
        $printlisteners = $listeners - $numberof_intromounts;
        echo "<td align=right><div class=$LINESTYLE style='background-color: lightgreen;'><span>".$printlisteners."</span></div></th>";
    }
    }
    echo "</tr>";
}
echo "</table>";
$db->close();
?>
