<?php

require_once( 'AMP/System/Data/Item.inc.php');

class ToolControl extends AMPSystem_Data_Item {

    var $datatable = "module_control";
    var $name_field = "description";

    function ToolControl ( &$dbcon, $id = null ) {
        $this->init( $dbcon, $id );
    }
}

?>
