<?php
require_once( 'AMP/User/Profile/Profile.php');

class AMP_User_Data extends AMP_User_Profile {
    var $datatable = 'userdata';
    var $_class_name = 'AMP_User_Data';

    function AMP_User_Data( $dbcon, $id = null ) {
        $this->__construct( $dbcon, $id );
    }
}
?>
