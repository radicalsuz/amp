<?php

require_once ( 'AMP/UserData/Plugin/Save.inc.php' );
require_once ( 'Modules/Schedule/Schedule.php' );

class UserDataPlugin_Save_AMPAppointment extends UserDataPlugin_Save {

	var $short_name = "Appointment";

	var $options = array(
		'_userid' => array( 'available' => false ),
		'schedule_id' => array( 
			'available' => true,
			'type' 	=> 'select',
			'default' => 1,
			'values'  => 'Lookup(schedules, id, name)' ),
		'_scheduleitem_id' => array( 'available' => false )
		);


	function UserDataPlugin_Save_AMPAppointment ( &$udm, $plugin_instance=null ) {
		$this->init( $udm, $plugin_instance );
	}

	function _register_fields_dynamic() {
		$options = $this->getOptions();
		$schedule = &new Schedule( $this->dbcon, $options['schedule_id'] );
		$schedule->readScheduleItems();
		
		$this->fields = array(
			'appointment' => array(
				'type' => 'radiogroup',
				'public' => true,
				'enabled' => true,
				'label' => 'Request Time',
				'required' => true,
				'values'  => $schedule->getOpenItems_Options_OwnerTime()
			)
		);

	}

	function getSaveFields() {
		return array( 'appointment' );
	}

	function save( $data ) {
		$options = $this->getOptions();
		$schedule = &new Schedule( $this->udm->dbcon, $options['schedule_id'] );
		$schedule->makeAppointment( $this->udm->uid, $data['appointment'] );
	}

}
?>
