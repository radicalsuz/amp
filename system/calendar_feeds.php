<?php

require_once('AMP/System/Page.inc.php');
require_once('Modules/Calendar/Feeds/CalendarFeeds.php');
require_once('Modules/Calendar/Feeds/ComponentMap.inc.php');

$map =& new ComponentMap_CalendarFeeds();
$page =& new AMPSystem_Page( $dbcon, $map );

if (isset($_GET['action']) && $_GET['action'] == "update") {
	$feeds =& new CalendarFeeds( $dbcon );
	if($feeds->update()) {
		$page->setMessage('Updated feeds');
	} else {
		$page->setMessage('Could not update feeds', $is_error = true);
	}
	$page->showList( true );
}

if (isset($_GET['action']) && $_GET['action'] == "list") {
	$page->showList( true );
}

$page->execute();
print $page->output();

?>
