<?php
require_once("AMP/BaseDB.php");
require_once("AMP/Geo/Maps.php");

$map_id = isset( $_GET['id']) && $_GET['id'] ? intval( $_GET['id'] ) : false;

$map = new Maps($dbcon, $map_id );
echo $map->google_xml();

?>
