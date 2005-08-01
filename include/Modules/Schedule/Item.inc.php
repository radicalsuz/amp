<?php

require_once( 'AMP/System/Data/Item.inc.php' );
require_once( 'Modules/Schedule/Appointment/Set.inc.php' );

define ('AMP_SCHEDULE_STATUS_OPEN', 'open');
define ('AMP_SCHEDULE_STATUS_CLOSED', 'closed');
define ('AMP_SCHEDULE_STATUS_DRAFT', 'draft');
class ScheduleItem extends AMPSystem_Data_Item {

    var $name_field = "title";
    var $datatable = "scheduleitems";

	var $_appointments;
    
    function ScheduleItem ( &$dbcon, $id = null ) {
        $this->init( $dbcon, $id );
    }

	function getStatus() {
		return $this->getData('status');
	}

	function setStatus( $status ) {
		$data = array ( 'status' => $status );
		return $this->mergeData( $data );
	}

	function getStatusOptions() {
		$options = filterConstants( 'AMP_SCHEDULE_STATUS' );
		$result = array();
		foreach (array_values($options) as $value) {
			$result[$value] = $value;
		}
		return $result;
	}

    function getContactNames() {
        if (! $schedule_id = $this->getData('schedule_id')) return false;
        
        $form_search =  &new FormLookup_FindScheduleForm( $schedule_id );

        if (! $form_id = $form_search->getResult() ) return false;

        return FormLookup_Names::instance( $form_id );
    }
        

    function describeSlot() {
        $contact_names = $this->getContactNames();
        $data = $this->getData();
        $output = "<div>";
        if (isset($data['title']) && $data['title']) {
            $output .= str_replace( "'", "&rsquot;", $data['title'] );
        }
        if (isset($data['start_time']) && $data['start_time']) {
            $output .= '<BR>' . date( 'M j, Y \a\t g:i A', strtotime($data['start_time']));
        }
        if (isset($data['owner_id']) && $data['owner_id'] && $contact_names ) {
            $output .= '<BR>' . $contact_names[$data['owner_id']] . "<BR>";
        }
        return $output . '</div>';
    }
		
	function isOpen() {
		if ($this->getStatus() == AMP_SCHEDULE_STATUS_OPEN) {
			return true;
		}
	}

	function isClosed() {
		if ($this->getStatus() == AMP_SCHEDULE_STATUS_CLOSED) {
			return true;
		}
	}

	function getCapacity() {
		return $this->getData('capacity');
	}

	function getAppointments() {
        trigger_error( 'GETTING APPOINTMENTS'.$this->id );
		if (isset($this->_appointments)) {
			return $this->_appointments;
		}	
        if (!isset($this->id)) return array();
        trigger_error( 'GETTING APPOINTMENTS' );

		$this->_appointments =  & new AppointmentSet ( $this->dbcon );
		$this->_appointments->setScheduleItemId( $this->id );
		$this->_appointments->readData();
		return $this->_appointments;
	}
		
	function appointmentsCount() {
		if(!( $appointments = $this->getAppointments())) return 0;
		return $appointments->RecordCount();	
	}

	function containsAppointment(&$appointment) {
		$myAppointments = $this->getAppointments();
		return (array_search($appointment, $myAppointments, true) !== false);
	}
		

	function updateStatus() {
        $capacity = $this->getCapacity();
        if (!$capacity && ($capacity !== 0)) return;
        trigger_error( $capacity . ' CAPACITY' );
        trigger_error ($this->appointmentsCount() . ' COUNT');
		if ( $this->appointmentsCount()  >= $capacity ) {
			$this->setStatus( AMP_SCHEDULE_STATUS_CLOSED );
			if (!$result = $this->save()) return false;
            $this->dbcon->CacheFlush();
            return $result;
		}
	}


}
?>
