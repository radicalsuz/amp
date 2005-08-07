<?php

require_once ('Modules/Calendar/Feeds/Set.inc.php' );
require_once ('AMP/System/List.inc.php');

class CalendarFeeds_List extends AMPSystem_List {

	var $col_headers = array( "ID" => "id", "Title" => "title", "URL" => "url"
							, "Link" => "link", "Description" => "description" );
	var $editlink = "calendar_feeds.php";
	var $name = "Calendar Feeds";

	function CalendarFeeds_List ( &$dbcon ) {
		$source = & new CalendarFeedsSet( $dbcon );
		$this->init ($source );
	}

}
?>
