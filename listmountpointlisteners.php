<?php
$mountpoint = $_GET['mnt'];
include("functions.php");

init_db();

error_reporting(E_ALL);

$db = new SQLite3('load.db');
$results = $db->query("SELECT mountpoint FROM t_pool where mountpoint like '%$mountpoint%' and mountpoint not like '/intro%' group by mountpoint order by mountpoint");

while ( $row = $results->fetchArray() ) {
    $listsum = 0;
    $mountpoint = ltrim ($row['mountpoint'], '/');

    $results2 = $db->query("SELECT sum(listeners) FROM t_pool where mountpoint like '$mountpoint'");
	while ( $row2 = $results2->fetchArray() ) {
	    $listsum_basic = $row2[0];
	}
    $results3 = $db->query("SELECT sum(listeners) FROM t_pool where mountpoint like '/intro.$mountpoint'");
	while ( $row3 = $results2->fetchArray() ) {
	    $listsum_intro = $row3[0];
	}

    # eliminating intro mountpoint self listening
    $intromounts = $db->query("SELECT listeners FROM t_pool where mountpoint like '/intro.$mountpoint'");
    $numberof_intromounts = 0; while ( $row = $intromounts->fetchArray() ) { $numberof_intromounts++; };

    $listsum = $listsum_basic + $listsum_intro - $numberof_intromounts;

    echo $listsum."@".$mountpoint."\n";
}
$db->close();

?>
