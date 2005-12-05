<?php

require_once( 'AMP/System/Data/Set.inc.php');
require_once( 'AMP/System/Permission/Permission.php');

class PermissionSet extends AMPSystem_Data_Set {
    var $datatable = 'permission';
    var $sort = array( 'perid');

    function PermissionSet ( &$dbcon ){
        $this->init( $dbcon );
    }

    function addCriteriaPermission( $perid ){
        $this->addCriteria( 'perid='.$perid );
    }

    function addCriteriaPermissionGroup( $groupid ){
        $this->addCriteria( 'groupid='.$groupid );
    }
}

?>
