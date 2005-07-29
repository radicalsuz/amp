<?php

require_once( 'AMP/UserData/Action.inc.php' );
define( 'AMP_USERDATA_ACTION_SCHEDULE', 'schedule' );

class Appointment extends UserData_Action {

    var $service = AMP_USERDATA_ACTION_SCHEDULE; 

	function Appointment ( &$dbcon, $id=null) {
		$this->init( $dbcon, $id );
	}

	function &createAppointment( $user, $scheduleItem ) {
		$dbcon =& AMP_Registry::getDbcon();
		$appointment =& new Appointment( $dbcon );

		$appointment->setParticipant( $user );
		$appointment->setScheduleItem( $scheduleItem );

		return $appointment;
	}

	function setParticipant( $userdata_id ) {
		$person = array( "userdata_id" => $userdata_id );
		$this->mergeData( $person );
	}

	function setScheduleItem( $scheduleitem_id ) {
		$scheduleItem = array( "action_id" => $scheduleitem_id );
		$this->mergeData( $scheduleItem );
	}	

}
?>
