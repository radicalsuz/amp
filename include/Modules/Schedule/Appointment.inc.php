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

    function save() {
        if (!$this->getData('action_id')) return false;
        $item = &new ScheduleItem( $this->dbcon, $this->getData('action_id'));

        $this->dbcon->StartTrans();
        $result= (PARENT::save() && $item->updateStatus());
        if (!$result) {
            $this->dbcon->FailTrans();
        }
        return $this->dbcon->CompleteTrans();
    }

    function deleteData( $id ) {
        $result = PARENT::deleteData( $id );

        if (!($this->getData('action_id') && $id==$this->id)) return $result;
        $item = &new ScheduleItem( $this->dbcon, $this->getData('action_id'));
        return ($result && $item->updateStatus());
    }

/*
    function save() {
        return $this->debugSave();
    }
*/
}
?>
