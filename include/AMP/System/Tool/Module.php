<?php

require_once( 'AMP/System/Data/Item.inc.php');

class Module extends AMPSystem_Data_Item {

    var $datatable = "modules";
    var $name_field = "name";

    function Module ( &$dbcon, $id = null ) {
        $this->init( $dbcon, $id );
    }
}

?>
