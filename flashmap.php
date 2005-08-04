<?php

include("AMP/BaseDB.php"); 
include("AMP/BaseTemplate.php"); 
require_once("AMP/Geo/Maps.php");

$map = new Maps($dbcon,$_GET['map']);

echo "<h3>".$map->P['title']."</h3>";
echo "<p>".$map->P['description']."</p>";
echo  $map->flash_map();

require_once("AMP/BaseFooter.php");
?>