<?php

require_once('AMP/System/Page.inc.php');
require_once('Modules/Calendar/Feeds/ComponentMap.inc.php');
require_once("feedonfeeds-0.1.9/init.php");

$map =& new ComponentMap_CalendarFeeds();
$page =& new AMPSystem_Page( $dbcon, $map );

if (isset($_GET['action']) && $_GET['action'] == "list") {
	$page->showList( true );
}

$page->execute();
print $page->output();

?>
