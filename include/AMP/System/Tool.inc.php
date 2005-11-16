<?php

require_once( 'AMP/System/Data/Item.inc.php');

class AMPSystem_Tool extends AMPSystem_Data_Item {

    var $datatable = 'modules';

    function AMPSystem_Tool( &$dbcon, $id = null ){
        $this->init( $dbcon, $id );
    }

    function _blankIdAction( ){
        $this->_setSourceIncrement( 100 );
    }
}
?>
