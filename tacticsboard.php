<?php
include("functions.php");

init_db();

error_reporting(E_ALL);

$db = new SQLite3('load.db');
$db->busyTimeout(1000);

$results = $db->query("SELECT machineip,bandwidth,bandwidthlimit,load,loadlimit,timestamp FROM t_pool group by machineip");
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

$results10 = $db->query("SELECT machineip FROM t_pool group by machineip");
$machinesumsum = 0;
while ( $row10 = $results10->fetchArray() ) {
    $machinesumsum += 1;
}

$results11 = $db->query("SELECT mountpoint FROM t_pool where mountpoint not like '%proxy%' and  mountpoint not like '%intro%' group by mountpoint");
$mountpointsumsum = 0;
while ( $row11 = $results11->fetchArray() ) {
    $mountpointsumsum += 1;
}


$results7 = $db->query("SELECT sum(listeners) FROM t_pool");
while ( $row7 = $results7->fetchArray() ) {
    $listenersumsum = $row7[0];
}
$printlistenersumsum = $listenersumsum;

$date = new DateTime();
$now = $date->getTimestamp();
$dead = ( $now - 60 );

echo "<div><span class=sumsum>".$printlistenersumsum." Listeners ||| ".$machinesumsum." Machines ||| ".$mountpointsumsum." Mountpoints</span></div>";
echo "<hr><p>";
echo "ATTENTION: BANDWIDH in KBIT/S ||| LOAD in &#37; ||| BANDWIDTH SUM WITHOUT PROXIES";
echo "<p><hr><p>";
echo "<table borderx=1>";
echo "<tr><th class=rotatex><div><span>TSTAMP</span></div></th><th class=rotatex><div><span>IP</span></div></th><th class=rotatex><div><span>BW</span></div></th><th class=rotatex><div><span>BWLIMIT</span></div></th><th class=rotatex><div><span>LOAD</span></div></th><th class=rotatex><div><span>LOADLIMIT</span></div></th><th class=rotatex><div><span>LISTENERS</span></div></th>";
    while ( $row2 = $results2->fetchArray() ) {
    $mountpoint = ltrim ($row2['mountpoint'], '/');
    echo "<th class=rotate><div><span>".$mountpoint."</span></div></th>";
    }
echo "</tr>\n";

echo "<tr><th class=sumx><div><span>&nbsp;</span></div></th><th class=sumx><div><span>&nbsp;</span></div></th><th class=sum><div align=right><span>".$bwsumsum."</span></div></th><th class=sumx><div><span>&nbsp;</span></div></th><th class=sumx><div><span>&nbsp;</span></div></th><th class=sumx><div><span>&nbsp;</span></div></th><th class=sum><div align=right><span>".$printlistenersumsum."</span></div></th>";
    while ( $row2 = $results2->fetchArray() ) {
    $mountpoint = ltrim ($row2['mountpoint'], '/');
    $results9 = $db->query("SELECT sum(listeners) FROM t_pool where mountpoint like \"%".$mountpoint."\" ");
    while ( $row9 = $results9->fetchArray() ) {
	$listenermntsum = $row9[0];
    }
    $printlistenermntsum = $listenermntsum;
    echo "<th class=sum ><div class=th align=right><span>".$printlistenermntsum."</span></div></th>";
    }
echo "</tr>\n";

$LINESTYLE = ''; $SPANSTYLE_CPU = ''; $SPANSTYLE_BW = '';
while ( $row = $results->fetchArray() ) {
    if ( $LINESTYLE == 'A' ) { $LINESTYLE = 'B'; } else { $LINESTYLE = 'A';};
    echo "<tr>";
    if ( $row['load'] >= $row['loadlimit'] && $row['loadlimit'] != 0 ) { $LINESTYLE = 'OVERLOAD'; $SPANSTYLE_CPU = 'REDTXT'; };
    if ( $row['bandwidth'] >= $row['bandwidthlimit'] && $row['bandwidthlimit'] != 0 ) { $LINESTYLE = 'OVERLOAD'; $SPANSTYLE_BW = 'REDTXT'; };
    if ( $row['load'] >= 90 ) { $LINESTYLE = 'OVERLOAD90'; };
    if ( $row['load'] >= 100 ) { $LINESTYLE = 'OVERLOAD100'; };
    if ( $row['timestamp'] < $dead ) { $LINESTYLE = 'DEAD'; };
    echo "<td align=right><div class=$LINESTYLE><span>".$row['timestamp']."</span></div></td>";
    echo "<td align=left><div class=$LINESTYLE><span>".$row['machineip']."</span></div></td>";
    echo "<td align=right><div class=$LINESTYLE><span class=$SPANSTYLE_BW>".$row['bandwidth']."</span></div></td>";
    echo "<td align=right><div class=$LINESTYLE><span>".$row['bandwidthlimit']."</span></div></td>";
    echo "<td align=right><div class=$LINESTYLE><span class=$SPANSTYLE_CPU>".$row['load']."</span></div></td>";
    echo "<td align=right><div class=$LINESTYLE><span>".$row['loadlimit']."</span></div></td>";
    $SPANSTYLE_CPU = ''; $SPANSTYLE_BW = '';
    $results5 = $db->query("SELECT sum(listeners) FROM t_pool where machineip like \"".$row['machineip']."\" ");
    while ( $row5 = $results5->fetchArray() ) {
        $listenersum = $row5[0];
    }
    $printlistenersum = $listenersum;

    echo "<td align=right><div class=$LINESTYLE><span>".$printlistenersum."</span></div></td>";

    while ( $row2 = $results2->fetchArray() ) {
	$mountpoint = ltrim ($row2['mountpoint'], '/');
	$results3 = $db->query("SELECT sum(listeners) FROM t_pool where timestamp > ".$dead." AND mountpoint like \"%".$mountpoint."\" and machineip like \"".$row['machineip']."\" ");
    
	$listeners = NULL;
	while ( $row3 = $results3->fetchArray() ) {
    	    $listeners = $row3[0];
	}
	if ( is_null($listeners) ) {
    	    echo "<td align=right><div class=$LINESTYLE><span>&nbsp;</span></div></td>";
	} else {
    	    $printlisteners = $listeners;
	    if ( $LINESTYLE == 'DEAD' ) {
		echo "<td align=right><div class=$LINESTYLE><span>".$printlisteners."</span></div></td>";
	    } else {
		if ( $mountpoint != 'proxy' ) {
		    echo "<td align=right><div class=$LINESTYLE style='background-color: lightgreen;'><span><a class=link2mount href='http://".$row['machineip']."/".$mountpoint."' >".$printlisteners."</a></span></div></td>";
		} else {
		    echo "<td align=right><div class=$LINESTYLE style='background-color: blue;color: white;'><span>PRXY</span></div></td>";
		}
	    }
	}
    }
    echo "</tr>\n";
}
echo "</table>";
$db->close();
?>
