<?php

require_once( 'AMP/System/List.inc.php' );
require_once( 'Modules/Schedule.inc.php' );

class ScheduleList extends AMPSystem_List {
	var $name = "Schedule";
	var $col_headers = array( "Name" => "title", "Start" => "start", "Status" => "status" );
	var $editlink = "schedule.php";

	function ScheduleList( &$dbcon ) {
		$source = & new Schedule( $dbcon );
		$this->init( $source );
		$this->lookups['attendees'] = $this->getParticipantCounts();
	}

	function getParticipantCounts() {
		if (!($items = $this->source->getSlots())) return false; 
		foreach ($items as $key => $slot) {
			$itemcount[ $slot->id ] = $slot->participantCount(); 
		} 
		return $itemcount;
	}
}
?>
