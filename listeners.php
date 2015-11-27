<?php
$mountpoint = $_GET['mnt'];
include("functions.php");

init_db();

error_reporting(E_ALL);

$db = new SQLite3('load.db');
$results = $db->query("SELECT sum(listeners) FROM t_pool where mountpoint like '%$mountpoint%'");
$intromounts = $db->query("SELECT listeners FROM t_pool where mountpoint like '/intro%$mountpoint%'");
$numberof_intromounts = $intromounts->numColumns();
while ( $row = $results->fetchArray() ) {
    if (empty($row[0])) {
	    echo "0\n";
    } else {
	    $number = $row[0] - $numberof_intromounts;
	    echo $row[0]."\n";
	    echo $numberof_intromounts."\n";
	    echo $number."\n";
    }
};
$db->close();
#echo "<hr>OK!<br>\n";

?>
