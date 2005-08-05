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

    function getTimeText( $which_time ="start") {
        if (!($sql_date = $this->getData( $which_time . '_time'))) return false;
        $output = date( 'M j, Y \a\t g:ia', strtotime($sql_date));
        if (!($timezone = $this->getTimeZoneText() )) return $output;

        return $output . " " . $timezone;
    }

    function getTimeZoneText() {
        if (!( $tz = $this->getData( 'timezone' ))) return false;
        return $this->timezones[ $tz ];
    }

    function getTitle() {
        return $this->getData('title');
    }

    function _safeOption( $text ) {
        return str_replace( "'", "&rsquot;", $text );
    }

    function getLocation() {
        return $this->getData('location');
    }

    function getOwnerEmail() {
        if (!($owner = $this->getOwnerId())) return false;
        $emails = AMPSystem_Lookup::instance( 'userDataEmails' );
        if (!isset($emails[ $owner ])) return false;
        return $emails[ $owner ];
    }

        

    function describeSlot() {
        $data = $this->getData();
        $output_items = array( $this->getTimeText() );

        if ($title = $this->getTitle() )        $output_items[] = $title;
        if ($contact = $this->getOwnerName() )  $output_items[] = "with ".$contact;
        if ($location= $this->getLocation())    $output_items[] = $location;

        return $this->_safeOption( join( " : ", $output_items ) );
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

    function getOwnerId() {
        return $this->getData( 'owner_id' );
    }

    function getOwnerName() {
        if (!($id = $this->getOwnerId() )) return false;
        if (!($names = $this->getContactNames() )) return false;
        if (!isset($names[ $id ])) return false;
        return $names[ $id ];
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
