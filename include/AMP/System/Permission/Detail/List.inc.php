<?php

require_once( 'AMP/System/List.inc.php');
require_once( 'AMP/System/Permission/Detail/Set.inc.php' );

class PermissionDetail_List extends AMPSystem_List {
    var $name = "Permissions";
    var $col_headers = array( 
        'name' => 'name',
        'ID'    => 'id');
    var $editlink = 'per_description.php';

    function PermissionDetail_List( &$dbcon ) {
        $source = & new PermissionDetailSet( $dbcon );
        $this->init( $source );
    }
}
?>
