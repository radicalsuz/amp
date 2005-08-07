<?php

require_once ( 'AMP/System/ComponentMap.inc.php' );

class ComponentMap_CalendarFeeds extends AMPSystem_ComponentMap {

	var $heading = "Calendar Feeds";
	var $nav_name = "calendarfeeds";

	var $paths = array(
		'fields' => 'Modules/Calendar/Feeds/Fields.xml',
		'list' => 'Modules/Calendar/Feeds/List.inc.php',
		'form' => 'Modules/Calendar/Feeds/Form.inc.php',
		'source' => 'Modules/Calendar/Feeds/CalendarFeeds.php' );


	var $components = array(
		'list' => 'CalendarFeeds_List',
		'form' => 'CalendarFeeds_Form',
		'source' => 'CalendarFeeds' );
}
?>
