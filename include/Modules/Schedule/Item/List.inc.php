<?php

require_once( 'AMP/System/List.inc.php' );
require_once( 'Modules/Schedule/Schedule.php' );

class ScheduleItem_List extends AMPSystem_List {
	var $name = "ScheduleItems";
	var $col_headers = array( "Name" => "title", "Start" => "start_time", "Status" => "status" );
	var $editlink = "schedule_item.php";

	function ScheduleItem_List( &$dbcon ) {
		$source = & new ScheduleItemSet( $dbcon );
		$this->init( $source );
//		$this->addLookup( 'attendees', $this->source->getParticipantCounts());
//		$this->addTranslation( 'start_date', '_formatDate' );
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
