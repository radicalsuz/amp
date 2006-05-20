<?php
require_once( 'AMP/System/Data/Item.inc.php');

class AMP_System_User_Profile extends AMPSystem_Data_Item {

    var $datatable = 'userdata';

    function AMP_System_User_Profile( &$dbcon, $id =null ){
        $this->init( $dbcon, $id );
    }

}

?>
