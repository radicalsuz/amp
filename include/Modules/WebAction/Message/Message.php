<?php

require_once( 'AMP/System/Data/Item.inc.php');

class WebActionMessage extends AMPSystem_Data_Item {
    var $datatable = 'action_history'

    function WebActionMessage( &$dbcon, $id = null ) {
        $this->init( $dbcon, $id );
    }
}
?>
