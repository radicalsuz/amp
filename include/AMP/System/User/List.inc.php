<?php

require_once( 'AMP/System/List/Form.inc.php');
require_once( 'AMP/System/User/User.php' );

class User_List extends AMPSystem_List {
    var $name = "User";
    var $col_headers = array( 
        'User' => 'name',
        'Permission Group' => '_render_permission_link',
        'ID'    => 'id');
    var $editlink = 'user.php';
    var $_url_add = 'user.php?action=new';
    var $name_field = 'name';
    var $_source_object = 'AMPSystem_User';

    function User_List( &$dbcon, $criteria = array( ) ) {
        $source = $this->_init_source( $dbcon, $criteria ) ;
        $this->init( $source );
        $this->addLookup( 'permission', AMPSystem_Lookup::instance( 'permissionGroups'));
    }

    function _render_permission_link( &$source ) {
        $permission_id = $source->getPermission( );
        $permission_name = $this->lookup( $permission_id, 'permission' );
        if ( $permission_name == $permission_id ) return $permission_name;

        $url = AMP_url_add_vars( AMP_SYSTEM_URL_PERMISSION_GROUP, array( 'id='.$permission_id ));
        $renderer = AMP_get_renderer( );

        return $renderer->link(  $url, $permission_name );
    }
}
?>
