<?php

require_once( 'AMP/System/Observer.php');

class AMP_System_UserData_Plugin_Observer extends AMP_System_Observer {

    function onSave( &$plugin ){
        $plugin->updateOptions( );
    }

    function onDelete( &$plugin ){
        $plugin->deleteOptions( );
    }

}

?>
