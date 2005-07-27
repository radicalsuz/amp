<?php

require_once ('Modules/Schedule/Set.inc.php' );
require_once ('AMP/System/List.inc.php');

class Schedule_List extends AMPSystem_List {

	var $col_headers = array( "ID" => "id", "Name" => "name" );
	var $editlink = "schedule.php";
	var $name = "Schedules";

	function Schedule_List ( &$dbcon ) {
		$source = & new ScheduleSet( $dbcon );
		$this->init ($source );
	}

}
?>
