<?php
/* unclear who might use this
 * appears to be broken and high-vulnerability
 * commenting for security ap-2008-07-03
 */
/*
include("AMP/BaseDB.php"); 
require_once("Modules/Rss/RssFeeds.php");

$F = new RssFeeds($dbcon);

if ($_REQUEST['url']) {
	$F->url = $_REQUEST['url'];
	include("AMP/BaseTemplate.php"); 
	echo $F->content_display();

} else if ($_REQUEST['feed']) {
	$F->feed = intval( $_REQUEST['feed'] );
	$F->load_feed();

	// deal with sectional placement
	if ($_GET['type'] ) { $F->section = $_GET['type']; }
	$MM_type= $F->section;
	include("AMP/BaseTemplate.php"); 
	echo $F->content_display();

} else {
	include("AMP/BaseTemplate.php"); 
	echo $F->feed_list();
}

require_once("AMP/BaseFooter.php");

*/
?>
