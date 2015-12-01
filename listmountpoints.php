<?php
$mountpoint = $_GET['mnt'];
include("functions.php");

init_db();

error_reporting(E_ALL);

$db = new SQLite3('load.db');
$results = $db->query("SELECT mountpoint FROM t_pool where mountpoint like '%$mountpoint%' and mountpoint not like '/intro%' group by mountpoint order by mountpoint");

while ( $row = $results->fetchArray() ) {
   echo $row['mountpoint'];
}
$db->close();

?>
