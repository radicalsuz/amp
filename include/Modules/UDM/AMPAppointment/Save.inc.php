<?php

require_once ( 'AMP/UserData/Plugin/Save.inc.php' );
require_once ( 'Modules/Schedule/Appointment.inc.php' );
require_once ( 'Modules/Schedule/Schedule.php' );
require_once ( 'Modules/Schedule/Lookups.inc.php' );
require_once ( 'Modules/Schedule/Appointment/Form.inc.php' );

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

        $open_appts =  $schedule->describeOpenItems();
        $header_prefix  =  'AMP_SCHEDULE_APPOINTMENT_FORM_TEXT_';
        $header_descriptor = 'AVAILABLE';
        $choose_default = null;
        if (isset( $_GET['action_id'] ) && ( $item_id = $_GET['action_id'] )) {
            if (!isset( $open_appts[ $item_id ] )) {
                $header_prefix .= 'REQUESTED_';
                $open_appts = array();
            } else {
                $single_appt = array();
                $single_appt[ $item_id ] = $open_appts[ $item_id ];
                $open_appts = $single_appt;
                $choose_default = $item_id;
            }
        }

        if (empty( $open_appts )|| !$open_appts)    $header_descriptor = 'UNAVAILABLE';

        $this->fields = array( 
            'Appointments' => array(
                'type' => 'header', 
                'label' => sprintf( constant( $header_prefix . $header_descriptor ), $schedule->getName() ),
                'enabled' => true, 
                'public' => true )
            );

		
        if (empty( $open_appts )|| !$open_appts) return;

		$this->fields[ 'scheduleitem_id'] = array(
				'type' => 'radiogroup',
				'public' => true,
				'enabled' => true,
				'default' => $choose_default,
                'label' => 'Available Times',
				'required' => true,
				'values'  => $open_appts
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
