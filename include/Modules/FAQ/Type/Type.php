<?php

require_once( 'AMP/System/Data/Item.inc.php');

class FAQ_Type extends AMPSystem_Data_Item {

    var $datatable = "faqtype";
    var $name_field = "type";
    var $_field_status = 'uselink';

    function FAQ_Type ( &$dbcon, $id = null ) {
        $this->init( $dbcon, $id );
    }
}

?>
