<?php
function init_db() {
$db = new SQLite3('load.db');
$db-> exec("CREATE TABLE IF NOT EXISTS t_pool(
   target TEXT PRIMARY KEY DEFAULT 'default', 
   machineip TEXT NOT NULL DEFAULT '0.0.0.0',
   bandwidth INTEGER NOT NULL DEFAULT 0,
   bandwidthlimit INTEGER NOT NULL DEFAULT 0, 
   load INTEGER NOT NULL DEFAULT 0, 
   loadlimit INTEGER NOT NULL DEFAULT 0, 
   listeners INTEGER NOT NULL DEFAULT 0, 
   mountpoint TEXT NOT NULL DEFAULT '/unknown',
   timestamp INTEGER NOT NULL DEFAULT 0)");
$db-> exec("CREATE TABLE IF NOT EXISTS t_listeners(
   fingerprint TEXT NOT NULL DEFAULT 'default', 
   mountpoint TEXT NOT NULL DEFAULT '/unknown',
   timestamp INTEGER NOT NULL DEFAULT 0)");
$db->close();
}

function cleanup_pool($maxagelimitstamp) {
$db = new SQLite3('load.db');
$db->busyTimeout(100);
$db->exec("DELETE FROM t_pool WHERE timestamp < $maxagelimitstamp");
$db->close();
}
function cleanup_listeners($maxagelimitstamp) {
$db = new SQLite3('load.db');
$db->busyTimeout(100);
$db->exec("DELETE FROM t_listeners WHERE timestamp < $maxagelimitstamp");
$db->close();
}
function cleanup_listeners_othermounts($maxagelimitstamp_othermounts,$fingerprint,$mountpoint) {
$db = new SQLite3('load.db');
$db->busyTimeout(100);
$db->exec("DELETE FROM t_listeners WHERE timestamp < $maxagelimitstamp_othermounts AND fingerprint = '$fingerprint' AND mountpoint != '$mountpoint'");
$db->close();
}

?>
