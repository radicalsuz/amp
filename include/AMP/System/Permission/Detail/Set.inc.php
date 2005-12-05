<?php

require_once( 'AMP/System/Data/Set.inc.php');
require_once( 'AMP/System/Permission/Detail/Detail.php');

class PermissionDetailSet extends AMPSystem_Data_Set {
    var $datatable = 'per_description';
    var $sort = array( "name");

    function PermissionDetailSet ( &$dbcon ){
        $this->addCriteriaLive( );
        $this->init( $dbcon );
    }

    function addCriteriaLive( ){
        $this->addCriteria( 'publish=1');
    }
}

?>
