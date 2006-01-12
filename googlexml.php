<?php
require_once("AMP/BaseDB.php");
require_once("AMP/Geo/Maps.php");

$map = new Maps($dbcon,$_GET['id']);
echo $map->google_xml();

?>