<?php

require_once( 'AMP/System/List.inc.php' );
require_once( 'Modules/Schedule/Schedule.php' );

class ScheduleList extends AMPSystem_List {
	var $name = "Schedule";
	var $col_headers = array( "Name" => "title", "Start" => "start", "Status" => "status" );
	var $editlink = "schedule.php";

	function ScheduleList( &$dbcon ) {
		$source = & new Schedule( $dbcon );
		$this->init( $source );
		$this->lookups['attendees'] = $this->source->getParticipantCounts();
	}

    function getPersonalSchedule( $userdata_id ) {
        $this->source->addCriteria( "userdata_id" . "=" . $userdata_id );
        $this->source->readData();
    }

}
?>
