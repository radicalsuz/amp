<?php

class UserDataPlugin_Start_AMPVoterGuide extends UserDataPlugin {

    var $_available = true;

    function UserDataPlugin_Start_AMPVoterGuide ( &$udm, $plugin_instance = null ) {
        $this->init( $udm, $plugin_instance );
    }

    function init( &$udm, $plugin_instance = null) {
        PARENT::init( $udm, $plugin_instance );

        $save = & $udm->registerPlugin( 'AMPVoterGuide', 'Save', $plugin_instance );
        $savePositions = &$udm->registerPlugin( 'AMPVoterGuide', 'PositionSave', $plugin_instance );

        $read = & $udm->registerPlugin( 'AMPVoterGuide', 'Read', $plugin_instance );
#$readPositions = & $udm->registerPlugin( 'AMPVoterGuide', 'PositionRead', $plugin_instance );
    }

    function execute() {
        //do nothing
    }
}
?>
