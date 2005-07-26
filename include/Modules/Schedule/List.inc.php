<?php

require_once( 'AMP/System/List.inc.php' );
require_once( 'Modules/Schedule/Schedule.php' );

class Schedule_List extends AMPSystem_List {
	var $name = "Schedule";
	var $col_headers = array( "Name" => "title", "Start" => "start_time", "Status" => "status" );
	var $editlink = "schedule_item.php";

	function Schedule_List( &$dbcon ) {
		$source = & new Schedule( $dbcon );
		$this->init( $source );
		$this->addLookup( 'attendees', $this->source->getParticipantCounts());
		$this->addTranslation( 'start_date', '_formatDate' );
	}

	function _formatDate($date) {
		return date(strtotime($date));
	}

    function getPersonalSchedule( $userdata_id ) {
        #$this->source->addCriteria( "service" . "=" . AMP_SCHEDULE_ITEMTYPE_USER);
        $this->source->addCriteria( "owner_id" . "=" . $userdata_id );
        $this->source->readData();
    }

}
?>
