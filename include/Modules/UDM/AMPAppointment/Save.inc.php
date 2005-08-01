<?php

require_once ( 'AMP/UserData/Plugin/Save.inc.php' );
require_once ( 'Modules/Schedule/Appointment.inc.php' );
require_once ( 'Modules/Schedule/Schedule.php' );
require_once ( 'Modules/Schedule/Lookups.inc.php' );

class UserDataPlugin_Save_AMPAppointment extends UserDataPlugin_Save {

	var $short_name = "Appointment";

	var $options = array(
		'schedule_id' => array( 
			'available' => true,
			'type' 	=> 'select',
			'default' => 1,
			'values'  => 'Lookup(schedules, id, name)' ),
        'appointments_header' => array(
            'available' => true,
            'default' => "Select %s Appointment",
            'type' => 'text' ),
        'appointments_unavailable_header' => array(
            'available' => true,
            'default' => "No %s Appointments Available",
            'type' => 'text' )
		);


	function UserDataPlugin_Save_AMPAppointment ( &$udm, $plugin_instance=null ) {
		$this->init( $udm, $plugin_instance );
	}

	function _register_fields_dynamic() {
		$options = $this->getOptions();
		$schedule = &new Schedule( $this->dbcon, $options['schedule_id'] );
        $open_appts =  $schedule->describeOpenItems();
        if (empty( $open_appts ) || !$open_appts) {
            $this->fields = array( 
                'Appointments' => array(
                    'type' => 'header', 
                    'label' => sprintf($options['appointments_unavailable_header'], $schedule->getData("name") ) ,
                    'enabled' => true, 
                    'public' => true )
                );

            return;
        }
		
		$this->fields = array(
            'Appointments' => array(
                'type'=> 'header',
                'label' => sprintf($options['appointments_header'], $schedule->getData("name") ),
                'public' => true,
                'enabled' => true ),
			'scheduleitem_id' => array(
				'type' => 'radiogroup',
				'public' => true,
				'enabled' => true,
				'required' => true,
				'values'  => $open_appts
			)
		);

	}

	function getSaveFields() {
		return array( 'scheduleitem_id' );
	}

	function save( $data ) {
		$options = $this->getOptions();
		$schedule = &new Schedule( $this->dbcon, $options['schedule_id'] );
        if (!( isset($data['scheduleitem_id']) && $data['scheduleitem_id'])) return true;
		if (!$schedule->makeAppointment( $this->udm->uid, $data['scheduleitem_id'] )) {
            $this->udm->errorMessage( "The requested schedule time is not avaiable" );
            return false;
        }
        return true;
	}

}
?>
