<?php
$mountpoint = $_GET['mnt'];
include("functions.php");

init_db();

error_reporting(E_ALL);

$db = new SQLite3('load.db');
$results = $db->query("SELECT sum(listeners) FROM t_pool");
$results2 = $db->query("SELECT sum(bandwidth) FROM t_pool where mountpoint not like '%proxy%' group by machineip");

# eliminating intro mountpoint self listening
$intromounts = $db->query("SELECT listeners FROM t_pool where mountpoint like '/intro%'");
$numberof_intromounts = 0; while ( $row = $intromounts->fetchArray() ) { $numberof_intromounts++; };

while ( $row = $results->fetchArray() ) {
    if (empty($row[0])) {
	$listnumber = 0;
    } else {
	$listnumber = $row[0] - $numberof_intromounts;
    }
}
while ( $row2 = $results2->fetchArray() ) {
    if (empty($row2[0])) {
	$bwsum = 0;
    } else {
	$bwsum = $row2[0];
    }
}

echo $bwsum."|".$listnumber."\n";

$db->close();
?>
