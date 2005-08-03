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
    var $timezones = array(
        'PST' => 'Pacific',
        'EST' => 'Eastern',
        'MST' => 'Mountain',
        'CST' => 'Central' );
    
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
        $output = "";
        if (isset($data['start_time']) && $data['start_time']) {
            $output .= date( 'M j, Y \a\t g:ia', strtotime($data['start_time']));
            if (isset($data['timezone']) && $data['timezone']) {
                $output .= " " . $this->timezones[ $data[ 'timezone' ] ];
            }
        }
        if (isset($data['title']) && $data['title']) {
            $output .= ' : ' . str_replace( "'", "&rsquot;", $data['title'] );
        }
        if (isset($data['owner_id']) && $data['owner_id'] && $contact_names ) {
            $output .= ' : with ' . $contact_names[$data['owner_id']] ;
        }
        if (isset($data['location']) && $data['location'] ) {
            $output .= ' : ' . $data['location'] ;
        }
        return $output;
    }
		
	function isOpen() {
		return ($this->getStatus() == AMP_SCHEDULE_STATUS_OPEN) ;
	}

	function isClosed() {
		return ($this->getStatus() == AMP_SCHEDULE_STATUS_CLOSED) ;
	}

	function isDraft() {
		return ($this->getStatus() == AMP_SCHEDULE_STATUS_DRAFT) ;
	}

	function getCapacity() {
		return $this->getData('capacity');
	}

	function getAppointments() {
		if (isset($this->_appointments)) {
			return $this->_appointments;
		}	
        if (!isset($this->id)) return array();

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
        if (!$capacity && ($capacity !== 0)) return true;
        if ($this->isDraft()) return true;
        $new_status = AMP_SCHEDULE_STATUS_OPEN;
		if ( $this->appointmentsCount()  >= $capacity ) {
            $new_status = AMP_SCHEDULE_STATUS_CLOSED;
        } 
        if ($this->getStatus() == $new_status) return true;

        $this->setStatus( $new_status );
        if (!$result = $this->save()) return false;

        $this->dbcon->CacheFlush();
        return $result;
	}


}
?>
