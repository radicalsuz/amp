<?php

include("AMP/BaseDB.php"); 
include("AMP/BaseTemplate.php"); 
require_once("AMP/Geo/Maps.php");
if ($_GET['map']) {
	$map = new Maps($dbcon,$_GET['map']);

	echo "<h3>".$map->P['title']."</h3>";
	echo "<p>".$map->P['description']."</p>";
	echo  $map->google_map();
	echo '<div id="map" style="width: 500px; height: 400px"></div>';
} else {
	$sql = 'select id, name, description from maps ';
	$M=$dbcon->CacheExecute($sql) or DIE($sql.$dbcon->ErrorMsg());
	while (!$M->EOF) {
		echo "<p><a href = 'googlemap.php?map=".$M->Fields("id")."'>".$M->Fields('name')."</a>  ".$M->Fields("description")."</p>";
		$M->MoveNext();
	}				
}

require_once("AMP/BaseFooter.php");
?>