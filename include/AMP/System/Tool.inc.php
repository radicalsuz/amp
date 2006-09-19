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

    function get_url_edit( ) {
        if ( !( isset( $this->id ) && $this->id )) return false;
        return AMP_Url_AddVars( AMP_SYSTEM_URL_TOOLS, array( 'id=' . $this->id ) );
    }
}
?>
