<?php

include("AMP/BaseDB.php"); 
include("AMP/BaseTemplate.php"); 
require_once("AMP/Geo/Maps.php");
if ($_GET['map']) {
	$map = new Maps($dbcon,$_GET['map']);

	echo "<h3>".$map->P['title']."</h3>";
	echo "<p>".$map->P['description']."</p>";
	if ($_REQUEST['zip'] && preg_match( '/\d{5}(-\d{4})?/', $_REQUEST['zip'])) {
		$geo = new Geo($dbcon);
		$geo->Zip = $_REQUEST['zip'];
		$geo->zip_lookup();
		$lat =$geo->lat;
		$lng =$geo->long;
		$zoom = 10;
		if ($_REQUEST['zoom']) { $zoom = intval( $_REQUEST['zoom'] ) ; }
	}
	if ( ($lat) && ($lng) ) {	
		echo  $map->google_map(500,400,$zoom,$lat,$lng);
	} else {
		echo  $map->google_map();
	}


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
