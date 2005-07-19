<?php

class Schedule extends AMPSystem_Data_Set {

	// or $_timeslots or $_scheduleElements or something
	var $_slots = array();

	var $_owner;
	var $datatable = "timeslots";

	function Schedule( &$dbcon, $id = null) {
		$this->init( $dbcon );
		if (isset($id)) $this->getUserSchedule( $id );
	}
    function getUserSchedule( $userdata_id ) {
        $this->source->addCriteria( "userdata_id = ".$userdata_id );
        if ($this->source->readData()) $this->_owner = $userdata_id;
		$this->buildSchedule();
    }

	function buildSchedule() {
		while ( $slot = $this->getData()) {	
			$current_slot = &new ScheduleTimeSlot( $dbcon );
            $current_slot->setData( $slot );
            $this->_slots[] = &$current_slot;
		}
	}
		
	function getOpenSlots() {
		$openslots = array();
		foreach ($this->_slots as $key => $item ) {
			if (!$item->isOpen()) continue;
			$openslots[] = &$this->_slots[$key];
		}
		return $openslots;
	}

	function getSlots() {
		return $this->_slots;
	}

	function getParticipantCounts() {
        /*
		if (!($items = $this->source->getSlots())) return false; 
		foreach ($items as $key => $slot) {
			$itemcount[ $slot->id ] = $slot->participantCount(); 
		} 
		return $itemcount;
        */
	}

}

require ('AMP/System/Data/Item.inc.php');
define ('AMP_TIMESLOT_STATUS_OPEN', 'open');
define ('AMP_TIMESLOT_STATUS_CLOSED', 'closed');
class ScheduleTimeSlot extends AMPSystem_Data_Item {

	var $datatable = 'timeslots';

	function ScheduleTimeSlot( &$dbcon, $id=null ) {
		$this->init( $dbcon, $id );
	}

	function getStatus() {
		return $this->getData('status');
	}

	function isOpen() {
		if ($this->getStatus() == AMP_TIMESLOT_STATUS_OPEN) {
			return true;
		}
	}

	function isClosed() {
		if ($this->getStatus() == AMP_TIMESLOT_STATUS_CLOSED) {
			return true;
		}
	}
	
	function participantCount() {
		$parp_set = &new UserData_Action_Schedule_Set( $this->dbcon,$this->id );
		return $parp_set->RecordCount();
	}

	function addParticipant( $participant_id ) {
		$new_slot = &new UserData_Action_Schedule( $this->dbcon,$this->id );
		$new_slot->setParticipant( $participant_id );
		$new_slot->save();
		if ($this->participantCount() >= $this->getCapacity()) {
			$this->setStatus( AMP_TIMESLOT_STATUS_CLOSED );
		}
	}

	function getCapacity() {
		return $this->getData('capacity');
	}

	function setStatus( $status ) {
		$data = array ( 'status' => $status );
		return $this->setData( $data );
	}
		 

}

class UserData_Action_Schedule extends AMPSystem_Data_Item {

	var $_action;
	var $_userId;
	var $_actionId;
	var $_timestamp;
	var $_created;
	var $_status;

	var $datatable = "userdata_action";

	function UserData_Action_Schedule ( &$dbcon, $id = null ) {
		$this->init( $dbcon, $id );
	}

	function setParticipant( $id ) {
		$person = array( "userdata_id" => $id );
		$this->setData( $person );
	}


}

define( 'AMP_USERDATA_ACTION_SCHEDULE', 'schedule' );
class UserData_Action_Schedule_Set extends AMPSystem_Data_Set {

	var $datatable = "userdata_action";

	function UserData_Action_Schedule_Set ( &$dbcon ) {
		$this->init( $dbcon );
	}

    function _register_criteria_dynamic() {
        $this->addCriteria( "action="
                    . $this->dbcon->qstr(AMP_USERDATA_ACTION_SCHEDULE));
    }

    function getParticpantCounts() {
        return $this->getGroupedIndex($this->getUserdataId());
    }

    function getUserdataId() {
        return "userdata_id";
    }
}
?>
