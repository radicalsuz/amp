<?php

require_once( 'AMP/System/List.inc.php' );
require_once( 'Modules/Schedule/Schedule.php' );
require_once( 'Modules/Schedule/Lookups.inc.php' );
require_once( 'Modules/Schedule/Appointment/Set.inc.php' );

class ScheduleItem_List extends AMPSystem_List {
	var $name = "ScheduleItems";
	var $col_headers = array( "Name" => "title", "Time" => "start_time", "Status" => "status", 'Schedule'=>'schedule_id' );
	var $editlink = "schedule_item.php";
    var $schedule_id;
    var $extra_columns = array( 'Participants'=>'schedule_appointment.php?action=list&scheduleitem_id=' );

	function ScheduleItem_List( &$dbcon, $id = null ) {
		$source = & new ScheduleItemSet( $dbcon );
		$this->init( $source );
        $this->addLookup( 'schedule_id', AMPSystem_Lookup::instance('scheduleNames') );
        $this->addLookup( 'quantity', $this->getAppointmentCounts() );
		#$this->addTranslation( 'id', 'lookupParticipantCount');
		$this->addTranslation( 'start_time', '_formatDate' );
	}

	function _formatDate($date) {
        return date( 'M j, Y g:ia', strtotime($date));
	}

    function getPersonalSchedule( $userdata_id ) {
        $this->source->addCriteria( "owner_id" . "=" . $userdata_id );
        $this->source->readData();
    }

    function getAppointmentCounts() {
        $source = &new AppointmentSet( AMP_Registry::getDbcon()  );
        return $source->getGroupedIndex( 'action_id' );
    }

    function lookupParticipantCount( $id ) {
        if (!isset($this->lookups['quantity'][$id])) return "";
        return $this->lookup( $id, 'quantity' );
    }


}
?>
