<?php
include("AMP/BaseDB.php"); 
require_once("Modules/Rss/RssFeeds.php");

$F = new RssFeeds($dbcon);

if ($_REQUEST['url']) {
	$F->url = $_REQUEST['url'];
	include("AMP/BaseTemplate.php"); 
	echo $F->content_display();

} else if ($_REQUEST['feed']) {
	$F->feed = $_REQUEST['feed'];
	//$F->load_feed()

	// deal with sectional placement
	if ($_GET['type'] ) { $F->section = $_GET['type'];}
	$MM_type= $F->section;
	echo $F->content_display();

} else {
	include("AMP/BaseTemplate.php"); 
	echo $F->feed_list();
}

require_once("AMP/BaseFooter.php");


?>