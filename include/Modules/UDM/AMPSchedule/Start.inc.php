<?php

require_once ('AMP/UserData/Plugin.inc.php' );

class UserDataPlugin_Start_AMPSchedule extends UserDataPlugin {

    var $options = array(
        'schedule_id' => array(
            'type' => 'select',
            'available' => true,
            'default' => 1,
            'label' => 'Schedule Name' )
        );

    function UserDataPlugin_Start_AMPSchedule ( &$udm, $plugin_instance=null ) {
        $this->init( $udm, $plugin_instance );
    }

	function init( &$udm, $plugin_instance=null ) {
		PARENT::init( $udm, $plugin_instance );

		$save =& $udm->registerPlugin('AMPSchedule', 'Save');
		$save->setOptions($this->getOptions());

		$read = $udm->registerPlugin('AMPSchedule', 'Read');
		$read->setOptions($this->getOptions());
	}

	function execute() {
		//do nothing
	}
}
?>
