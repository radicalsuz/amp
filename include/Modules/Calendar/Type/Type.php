<?php

require_once( 'AMP/System/Data/Item.inc.php');

class Calendar_Type extends AMPSystem_Data_Item {

    var $datatable = "eventtype";
    var $name_field = "name";

    function Calendar_Type ( &$dbcon, $id = null ) {
        $this->init( $dbcon, $id );
    }
}

?>
