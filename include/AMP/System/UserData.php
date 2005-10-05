<?php

require_once( 'AMP/System/Data/Item.inc.php');

class AMPSystem_UserData extends AMPSystem_Data_Item {

    var $datatable = "userdata_fields";

    function AMPSystem_UserData( &$dbcon, $id = null ){
        $this->init( $dbcon, $id );
    }

}

?>
