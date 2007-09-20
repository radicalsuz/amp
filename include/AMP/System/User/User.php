<?php

require_once( 'AMP/System/Data/Item.inc.php');

class AMPSystem_User extends AMPSystem_Data_Item {

    var $datatable = "users";
    var $name_field = "name";
    var $class_name = 'AMPSystem_User';

    function AMPSystem_User ( &$dbcon, $id = null ) {
        $this->init( $dbcon, $id );
    }

    function getAllowedPages( ) {
        if ( !( $pages = $this->getData( 'system_allow_only'))) return false;
        return preg_split( "/\s?\n?,\s?\n?/", $pages );
    }

    function getURL( ){
        return $this->getURLHome( );
    }

    function getURLHome( ){
        if ( !( $home_url = $this->getData('system_home' ))) return AMP_SYSTEM_URL_HOME;
        return $home_url;
    }

    function hasPageRestrictions( ){
        return $this->getData( 'system_allow_only');
    }

    function isAllowedPage( $url ){
        if ( !( $pages = $this->getAllowedPages( ))) return true;
        if ( $url == $this->getURLHome( )) return true;
        return ( array_search( $url, $pages ) !== FALSE );
    }

    function getPermissionGroup( ){
        return $this->getData( 'permission' );
    }

    function validatePassword( $password ){
        if ( !$password ) return false;
        if ( !( $valid_password = $this->getData( 'password'))) return false;
        return ( $password == $valid_password );
    }

    function readUsername( $username ){
        $names = AMP_lookup( 'users');
        $key = array_keys( $names, $username );
        if ( !empty( $key )) {
            $user_id = current( $key );
        }
        //$this->addCriteria( 'name='.$this->dbcon->qstr( $username));
        $this->readData( $user_id );
    }

    function hasPermission( $permission_value ){
        require_once( 'AMP/System/Permission/Manager.inc.php');
        $manager = &new AMPSystem_PermissionManager( );
        $manager->readUser( $this->getName( ));
        return $manager->authorized( $permission_value );
    }

    function getPermission( ) {
        return $this->getData( 'permission');
    }

    function getGroup( ) {
        return $this->getPermission( );
    }

    function _afterSave( ) {
        AMP_permission_update( );
    }

    function get_url_edit( ) {
        return $this->get_system_url( 'user' );
    }

}

?>
