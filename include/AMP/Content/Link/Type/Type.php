<?php

require_once( 'AMP/System/Data/Item.inc.php');

class Link_Type extends AMPSystem_Data_Item {

    var $datatable = "linktype";
    var $name_field = "name";

    function Link_Type ( &$dbcon, $id = null ) {
        $this->init( $dbcon, $id );
    }
}

?>
