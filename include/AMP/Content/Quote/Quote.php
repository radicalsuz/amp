<?php

require_once( 'AMP/System/Data/Item.inc.php');

class Quote extends AMPSystem_Data_Item {

    var $datatable = "quotes";
    var $name_field = "quote";

    function Quote ( &$dbcon, $id = null ) {
        $this->init( $dbcon, $id );
    }

    function getSection( ) {
        return $this->getData( 'type');
    }

    function _adjustSetData( $data ) {
        $this->legacyFieldname( $data, 'type', 'section' );
    }
}

?>
