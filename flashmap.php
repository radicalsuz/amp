<?php

include("AMP/BaseDB.php"); 
include("AMP/BaseTemplate.php"); 
require_once("AMP/Geo/Maps.php");
$map_id = isset( $_GET['map']) && $_GET['map'] ? intval( $_GET['map']) : false;
if ($map_id) {
	$map = new Maps($dbcon,$map_id);

	echo "<h3>".$map->P['title']."</h3>";
	echo "<p>".$map->P['description']."</p>";
	echo  $map->flash_map();
} else {
	$sql = 'select id, name, description from maps ';
	$M=$dbcon->CacheExecute($sql);
	while ($M && !$M->EOF) {
		echo "<p><a href = 'flashmap.php?map=".$M->Fields("id")."'>".$M->Fields('name')."</a>  ".$M->Fields("description")."</p>";
		$M->MoveNext();
	}				
}

require_once("AMP/BaseFooter.php");
?>
