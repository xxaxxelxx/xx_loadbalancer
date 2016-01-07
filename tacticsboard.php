<?php
#$mountpoint = $_GET['mnt'];
include("functions.php");

init_db();

error_reporting(E_ALL);

$db = new SQLite3('load.db');
$results = $db->query("SELECT machineip,bandwidth,bandwidthlimit,load,timestamp FROM t_pool group by machineip");
$results2 = $db->query("SELECT mountpoint FROM t_pool where mountpoint not like \"/intro.%\" group by mountpoint order by mountpoint");

header("refresh: 60;");
echo '<head><title>TACTICS</title></head>';
echo "<style>";
include 'tacticsboard.css';
echo "</style>";
echo "<div><span class=headline>TACTICS</span></div><p>";
echo "<div><span class=tstamp>".date(DATE_RFC822)."</span></div>";
echo "<hr><br>";

$results9 = $db->query("SELECT bandwidth,machineip FROM t_pool where mountpoint not like '%proxy%' group by machineip");
$bwsumsum = 0;
while ( $row9 = $results9->fetchArray() ) {
    $bwsumsum += $row9[0];
}

$results7 = $db->query("SELECT sum(listeners) FROM t_pool");
while ( $row7 = $results7->fetchArray() ) {
    $listenersumsum = $row7[0];
}
#$intromounts7 = $db->query("SELECT listeners FROM t_pool where mountpoint like '/intro%'");
#$numberof_intromounts7 = 0; while ( $row8 = $intromounts7->fetchArray() ) { $numberof_intromounts7++; };
#$printlistenersumsum = $listenersumsum - $numberof_intromounts7;
$printlistenersumsum = $listenersumsum;

$date = new DateTime();
$now = $date->getTimestamp();
$dead = ( $now - 60 );

echo "<div><span class=sumsum>".$printlistenersumsum."</span></div>";
echo "<hr><p>";
echo "ATTENTION: BANDWIDH in KBIT/S ||| LOAD in &#37; ||| BANDWIDTH SUM WITHOUT PROXIES";
echo "<p><hr><p>";
echo "<table borderx=1>";
echo "<tr><th class=rotatex><div><span>TSTAMP</span></div></th><th class=rotatex><div><span>IP</span></div></th><th class=rotatex><div><span>BW</span></div></th><th class=rotatex><div><span>BWLIMIT</span></div></th><th class=rotatex><div><span>LOAD</span></div></th><th class=rotatex><div><span>LISTENERS</span></div></th>";
    while ( $row2 = $results2->fetchArray() ) {
    $mountpoint = ltrim ($row2['mountpoint'], '/');
    echo "<th class=rotate><div><span>".$mountpoint."</span></div></th>";
    }
echo "</tr>\n";

echo "<tr><th class=sumx><div><span>&nbsp;</span></div></th><th class=sumx><div><span>&nbsp;</span></div></th><th class=sum><div align=right><span>".$bwsumsum."</span></div></th><th class=sumx><div><span>&nbsp;</span></div></th><th class=sumx><div><span>&nbsp;</span></div></th><th class=sum><div align=right><span>".$printlistenersumsum."</span></div></th>";
    while ( $row2 = $results2->fetchArray() ) {
    $mountpoint = ltrim ($row2['mountpoint'], '/');
    $results9 = $db->query("SELECT sum(listeners) FROM t_pool where mountpoint like \"%".$mountpoint."\" ");
    while ( $row9 = $results9->fetchArray() ) {
	$listenermntsum = $row9[0];
    }
#    $intromounts3 = $db->query("SELECT listeners FROM t_pool where mountpoint like '/intro%".$mountpoint."'");
#    $numberof_intromounts3 = 0; while ( $row10 = $intromounts3->fetchArray() ) { $numberof_intromounts3++; };
#    $printlistenermntsum = $listenermntsum - $numberof_intromounts3;
    $printlistenermntsum = $listenermntsum;
    echo "<th class=sum ><div class=th align=right><span>".$printlistenermntsum."</span></div></th>";
    }
echo "</tr>\n";

$LINESTYLE = '';
while ( $row = $results->fetchArray() ) {
    if ( $LINESTYLE == 'A' ) { $LINESTYLE = 'B'; } else { $LINESTYLE = 'A';};
    echo "<tr>";
    if ( $row['timestamp'] < $dead ) { $LINESTYLE = 'DEAD'; };
    if ( $row['load'] >= 75 ) { $LINESTYLE = 'OVERLOAD70'; };
    if ( $row['load'] >= 90 ) { $LINESTYLE = 'OVERLOAD80'; };
    if ( $row['load'] >= 100 ) { $LINESTYLE = 'OVERLOAD90'; };
    echo "<td align=right><div class=$LINESTYLE><span>".$row['timestamp']."</span></div></td>";
    echo "<td align=left><div class=$LINESTYLE><span>".$row['machineip']."</span></div></td>";
    echo "<td align=right><div class=$LINESTYLE><span>".$row['bandwidth']."</span></div></td>";
    echo "<td align=right><div class=$LINESTYLE><span>".$row['bandwidthlimit']."</span></div></td>";
    echo "<td align=right><div class=$LINESTYLE><span>".$row['load']."</span></div></td>";
    $results5 = $db->query("SELECT sum(listeners) FROM t_pool where machineip like \"".$row['machineip']."\" ");
    while ( $row5 = $results5->fetchArray() ) {
        $listenersum = $row5[0];
    }
#    $intromounts2 = $db->query("SELECT listeners FROM t_pool where machineip like \"".$row['machineip']."\" and mountpoint like '/intro%'");
#    $numberof_intromounts2 = 0; while ( $row6 = $intromounts2->fetchArray() ) { $numberof_intromounts2++; };
#    $printlistenersum = $listenersum - $numberof_intromounts2;
    $printlistenersum = $listenersum;

    echo "<td align=right><div class=$LINESTYLE><span>".$printlistenersum."</span></div></td>";

    while ( $row2 = $results2->fetchArray() ) {
	$mountpoint = ltrim ($row2['mountpoint'], '/');
	$results3 = $db->query("SELECT sum(listeners) FROM t_pool where mountpoint like \"%".$mountpoint."\" and machineip like \"".$row['machineip']."\" ");
    
	$listeners = NULL;
	while ( $row3 = $results3->fetchArray() ) {
    	    $listeners = $row3[0];
	}
	if ( is_null($listeners) ) {
    	    echo "<td align=right><div class=$LINESTYLE><span>&nbsp;</span></div></td>";
	} else {
#    	    $intromounts = $db->query("SELECT listeners FROM t_pool where machineip like \"".$row['machineip']."\" and mountpoint like '/intro.$mountpoint'");
#    	    $numberof_intromounts = 0; while ( $row4 = $intromounts->fetchArray() ) { $numberof_intromounts++; };
#    	    $printlisteners = $listeners - $numberof_intromounts;
    	    $printlisteners = $listeners;
	    if ( $LINESTYLE == 'DEAD' ) {
		echo "<td align=right><div class=$LINESTYLE><span>".$printlisteners."</span></div></td>";
	    } else {
		if ( $mountpoint != 'proxy' ) {
		    echo "<td align=right><div class=$LINESTYLE style='background-color: lightgreen;'><span><a class=link2mount href='http://".$row['machineip']."/".$mountpoint."' >".$printlisteners."</a></span></div></td>";
		} else {
		    echo "<td align=right><div class=$LINESTYLE style='background-color: blue;'><span>PROXY</span></div></td>";
		}
	    }
	}
    }
    echo "</tr>\n";
}
echo "</table>";
$db->close();
?>
