<?php

require_once ( 'AMP/UserData/Action.inc.php' );
require_once ( 'Modules/Schedule/Appointment.inc.php' );

class AppointmentSet extends UserData_Action_Set {

    var $service = AMP_USERDATA_ACTION_SCHEDULE;

	function AppointmentSet ( &$dbcon ) {
		$this->init( $dbcon );
	}

    function getParticpantCounts() {
        return $this->getGroupedIndex('userdata_id');
    }

    function setScheduleItemId ( $scheduleitem_id ) {
        $this->addCriteria( "action_id=" . $scheduleitemid );
    }
}
?>
