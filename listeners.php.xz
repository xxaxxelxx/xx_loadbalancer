<?php
$mountpoint = $_GET['mnt'];
include("functions.php");

init_db();

error_reporting(E_ALL);

$db = new SQLite3('load.db');
$results = $db->query("SELECT sum(listeners) FROM t_pool where mountpoint like '%$mountpoint%'");

# eliminating intro mountpoint self listening
$intromounts = $db->query("SELECT listeners FROM t_pool where mountpoint like '/intro%$mountpoint%'");
$numberof_intromounts = 0; while ( $row = $intromounts->fetchArray() ) { $numberof_intromounts++; };

while ( $row = $results->fetchArray() ) {
    if (empty($row[0])) {
	    echo "0\n";
    } else {
	    $number = $row[0] - $numberof_intromounts;
	    if ( $number < 0 ) { $number = 0; };
	    echo $number."\n";
    }
};
$db->close();
?>
