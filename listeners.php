<?php
$mountpoint = $_GET['mnt'];
include("functions.php");

init_db();

error_reporting(E_ALL);

$db = new SQLite3('load.db');
$results = $db->query("SELECT sum(listeners) FROM t_pool where mountpoint like '%$mountpoint%'");

while ( $row = $results->fetchArray() ) {
    if (empty($row[0])) {
	    echo "0\n";
    } else {
	    echo $row[0]."\n";
    }
};
$db->close();
#echo "<hr>OK!<br>\n";

?>
