<?php

require_once ('AMP/UserData/Plugin.inc.php' );

class UserDataPlugin_Start_AMPAppointment extends UserDataPlugin {

    var $options = array(
        'schedule_id' => array(
            'type' => 'select',
            'available' => true,
            'default' => 1,
            'label' => 'Schedule Name' ),
        'email_contact_schedule' => array(
            'type' => 'select',
            'available' => true,
            'default' => false,
            'label' => 'Confirmation Email Template for Schedule Contact' ),
        'email_contact_appointment' => array(
            'type' => 'select',
            'available' => true,
            'default' => false,
            'label' => 'Confirmation Email Template for Appointment Contact' )
        );

    function UserDataPlugin_Start_AMPAppointment ( &$udm, $plugin_instance=null ) {
        $this->init( $udm, $plugin_instance );
    }

	function init( &$udm, $plugin_instance=null ) {
		parent::init( $udm, $plugin_instance );

		$save =& $udm->registerPlugin('AMPAppointment', 'Save', $plugin_instance );
		#$save->setOptions($this->getOptions());

		$read = $udm->registerPlugin('AMPAppointment', 'Read', $plugin_instance);
//		$read->setOptions($this->getOptions());
	}

	function execute() {
		//do nothing
	}
}
?>
