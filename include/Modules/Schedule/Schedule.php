<?php

/*/Schedule object holds all the slots

pulls data from db to populate itself

determines characteristics of schedule such as availability

this is a schedule table wrapper
*/
require_once ("Modules/Schedule/Item/Set.inc.php");
require_once ("AMP/System/Data/Item.inc.php");
require_once ("AMP/UserData/Set.inc.php");
//class Schedule extends AMPSystem_Data_Set {
class Schedule extends AMPSystem_Data_Item {

	//var $_scheduleItems = array();
	var $_scheduleItems;

	var $_owner;
	//var $datatable = "scheduleitems";
	var $datatable = "schedules";

	function Schedule( &$dbcon, $id = null) {
		$this->init( $dbcon, $id );
	}

	function readScheduleItems() {
		$this->_scheduleItems =& new ScheduleItemSet($this->dbcon, $this->id);
		$this->_scheduleItems->readData();
		return $this->_scheduleItems;
	}

	function getOpenItems_Options_OwnerTime() {
		$slots = $this->_scheduleItems->getOpenItems();
		$options = array();

		$userset =& new UserDataSet($this->dbcon, 50, true);
		$search_options = array( 'clear_criteria' => array( 'value' => 'true' ),
								 'criteria' => array( 'value' => array('modin=50')) );
		$userset->doAction('Search', $search_options);
		$lookup = $userset->getNameLookup(); 

		foreach( $slots as $item ) {
			$useful_time = date( 'm/d/y \a\t h:i a', strtotime($item->getData('start_time')));
			$options[ $item->id ] = $lookup[$item->getData('owner_id')] . " : " . $useful_time;
		} 
		return $options;
	}
		
	function makeAppointment($user, $scheduleitem_id) {
		$appointment = Appointment::createAppointment($user, $scheduleitem_id);
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

	var $datatable = "userdata_actions";

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
