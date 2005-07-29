<?php

require_once ( 'AMP/UserData/Plugin/Save.inc.php' );
require_once ( 'Modules/Schedule/Appointment.inc.php' );
require_once ( 'Modules/Schedule/Schedule.php' );

class UserDataPlugin_Save_AMPAppointment extends UserDataPlugin_Save {

	var $short_name = "Appointment";

	var $options = array(
		'schedule_id' => array( 
			'available' => true,
			'type' 	=> 'select',
			'default' => 1,
			'values'  => 'Lookup(schedules, id, name)' )
		);


	function UserDataPlugin_Save_AMPAppointment ( &$udm, $plugin_instance=null ) {
		$this->init( $udm, $plugin_instance );
	}

	function _register_fields_dynamic() {
		$options = $this->getOptions();
		$schedule = &new Schedule( $this->dbcon, $options['schedule_id'] );
		$schedule->readScheduleItems();
		
		$this->fields = array(
            'Appointments' => array(
                'type'=> 'header',
                'values' => 'Select a ' . $schedule->getData('name') . ' Appointment',
                'public' => true,
                'enabled' => true ),
			'appointment' => array(
				'type' => 'radiogroup',
				'public' => true,
				'enabled' => true,
				'required' => true,
				'values'  => $schedule->describeOpenItems()
			)
		);

	}

	function getSaveFields() {
		return array( 'appointment' );
	}

	function save( $data ) {
		$options = $this->getOptions();
		$schedule = &new Schedule( $this->udm->dbcon, $options['schedule_id'] );
		if (!$schedule->makeAppointment( $this->udm->uid, $data['appointment'] )) {
            $this->udm->errorMessage( "The requested schedule time is not avaiable" );
            return false;
        }
        return true;
	}

}
?>
