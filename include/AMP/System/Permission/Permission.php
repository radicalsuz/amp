<?php

require_once( 'AMP/System/Data/Item.inc.php');

class Permission extends AMPSystem_Data_Item {

    var $datatable = "permission";

    function Permission ( &$dbcon, $id = null ) {
        $this->init( $dbcon, $id );
    }
}

?>
