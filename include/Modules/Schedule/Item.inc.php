<?php

require_once( 'AMP/System/Data/Item.inc.php' );

define ('AMP_TIMESLOT_STATUS_OPEN', 'open');
define ('AMP_TIMESLOT_STATUS_CLOSED', 'closed');
class ScheduleItem extends AMPSystem_Data_Item {

    var $name_field = "title";
    var $datatable = "timeslots";

	var $_appointments = array();
    
    function ScheduleItem ( &$dbcon, $id = null ) {
        $this->init( $dbcon, $id );
    }

	function getStatus() {
		return $this->getData('status');
	}

	function setStatus( $status ) {
		$data = array ( 'status' => $status );
		return $this->setData( $data );
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

	function getCapacity() {
		return $this->getData('capacity');
	}

	function getAppointments() {
		if (isset($this->_appointments)) {
			return $this->_appointments;
		}	

		$this->_appointments =  & new AppointmentSet ( $this->dbcon );
		$this->_appointments->addCriteria( "action_id=" . $this->id );
		$this->_appointments->readData();
		return $this->_appointments;
	}
		
	function appointmentsCount() {
		$appointments = $this->getAppointments();
		return $appointments->RecordCount();	
	}

	function update(&$appointment) {
		//if we don't know about this appointment, increment count, and maybe close
		if( $this->isOpen() )
	}

	function addAppointment() {
		if ( $this->appointmentsCount()  >= $this->getCapacity() ) {
			$this->setStatus( AMP_TIMESLOT_STATUS_CLOSED );
			$this->save();
		}
	}

}
?>
