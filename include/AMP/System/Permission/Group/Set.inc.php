<?php

require_once( 'AMP/System/Data/Set.inc.php');
require_once( 'AMP/System/Permission/Group/Group.php');

class PermissionGroupSet extends AMPSystem_Data_Set {
    var $datatable = 'per_group';
    var $sort = array( "name");

    function PermissionGroupSet ( &$dbcon ){
        $this->init( $dbcon );
    }
}

?>
