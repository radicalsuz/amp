<?php

/*/Schedule object holds all the slots

pulls data from db to populate itself

determines characteristics of schedule such as availability

this is a schedule table wrapper
*/
require_once ("Modules/Schedule/Item.inc.php");
class Schedule extends AMPSystem_Data_Set {

	// or $_timeslots or $_scheduleElements or something
	var $_slots = array();

	var $_owner;
	var $datatable = "timeslots";

	function Schedule( &$dbcon, $id = null) {
		$this->init( $dbcon );
		if (isset($id)) $this->getUserSchedule( $id );
	}

    function getScheduleByOwner( $owner_id ) {
        $this->addCriteria( "owner_id = ".$owner_id );
        if ($this->readData()) $this->_owner = $owner_id;
		$this->buildSchedule();
    }

	function getUserSchedule( $id ) {
	}
		

	function buildSchedule() {
		if ( !$this->isReady() ) return false;

		while ( $slot = $this->getData()) {	
			$current_slot = &new ScheduleItem( $dbcon );
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

	function makeAppointment($user, $slot) {
		$appointment = Appointment::createAppointment($user, $slot);
		return $appointment->save();
	}
}

class Appointment extends AMPSystem_Data_Item {

	var $_action;
	var $_userId;
	var $_actionId;
	var $_timestamp;
	var $_created;
	var $_status;

	var $datatable = "userdata_action";

	function Appointment ( &$dbcon, $id=null) {
		$this->init( $dbcon, $id );
	}

	function init( &$dbcon, $id=null ) {
		PARENT::init( $dbcon, $id );
		$this->setService();
	}

	function &createAppointment( $user, $scheduleItem ) {
		$dbcon =& AMP_Registry::getDbcon();
		$appointment =& new Appointment( $dbcon );

		$appointment->setParticipant( $user );
		$appointment->setScheduleItem( $scheduleItem );

		return $appointment;
	}

	function setService() {
		$service = array( "service" => AMP_USERDATA_ACTION_SCHEDULE );
		$this->setData( $service );
	}
		
	function setParticipant( $userdata_id ) {
		$person = array( "userdata_id" => $userdata_id );
		$this->setData( $person );
	}

	function setScheduleItem( $scheduleitem_id ) {
		$scheduleItem = array( "action_id" => $scheduleitem_id );
		$this->setData( $scheduleItem );
	}	

	function save() {
		$status = PARENT::save();

		if ($status) {
			$scheduleItem =& new ScheduleItem($this->dbcon, $this->getData("action_id"));
			if( !$scheduleItem->update($this) ) return false;

			//-----
			$status = false;
			if($scheduleItem->containsAppointment($this) || $scheduleItem->isOpen()) {
				$status = PARENT::save();
			}
			$scheduleItem->updateStatus();
		}
	}
}

define( 'AMP_USERDATA_ACTION_SCHEDULE', 'schedule' );
class AppointmentSet extends AMPSystem_Data_Set {

	var $datatable = "userdata_action";

	function AppointmentSet ( &$dbcon ) {
		$this->init( $dbcon );
	}

    function _register_criteria_dynamic() {
        $this->addCriteria( "action="
                    . $this->dbcon->qstr(AMP_USERDATA_ACTION_SCHEDULE));
    }

    function getParticpantCounts() {
        return $this->getGroupedIndex('userdata_id');
    }

    function getUserdataId() {
        return "userdata_id";
    }
}
?>
