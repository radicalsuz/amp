<?php

require_once( 'AMP/System/Data/Set.inc.php');
require_once( 'AMP/System/User/User.php');

class UserSet extends AMPSystem_Data_Set {
    var $datatable = 'users';
    var $sort = array( "name");

    function UserSet ( &$dbcon ){
        $this->init( $dbcon );
    }
}

?>
