<?php

require_once ('AMP/UserData/Plugin.inc.php' );

class UserDataPlugin_Start_AMPAppointment extends UserDataPlugin {

    var $options = array(
        'schedule_id' => array(
            'type' => 'select',
            'available' => true,
            'default' => 1,
            'label' => 'Appointment Name' )
        );

    function UserDataPlugin_Start_AMPAppointment ( &$udm, $plugin_instance=null ) {
        $this->init( $udm, $plugin_instance );
    }

	function init( &$udm, $plugin_instance=null ) {
		PARENT::init( $udm, $plugin_instance );

		$save =& $udm->registerPlugin('AMPAppointment', 'Save');
		$save->setOptions($this->getOptions());

//		$read = $udm->registerPlugin('AMPAppointment', 'Read');
//		$read->setOptions($this->getOptions());
	}

	function execute() {
		//do nothing
	}
}
?>
