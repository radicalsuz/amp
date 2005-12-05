<?php

require_once( 'AMP/System/List.inc.php');
require_once( 'AMP/System/User/Set.inc.php' );

class User_List extends AMPSystem_List {
    var $name = "User";
    var $col_headers = array( 
        'User' => 'name',
        'Permission Group' => 'permission',
        'ID'    => 'id');
    var $editlink = 'users.php';

    function User_List( &$dbcon ) {
        $source = & new UserSet( $dbcon );
        $this->init( $source );
        $this->addLookup( 'permission', AMPSystem_Lookup::instance( 'permissionGroups'));
    }
}
?>
