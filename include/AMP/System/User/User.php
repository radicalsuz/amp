<?php

require_once( 'AMP/System/Data/Item.inc.php');

class AMPSystem_User extends AMPSystem_Data_Item {

    var $datatable = "users";
    var $name_field = "name";

    function AMPSystem_User ( &$dbcon, $id = null ) {
        $this->init( $dbcon, $id );
    }
}

?>
