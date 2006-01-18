<?php

/* * * * * * *
 *  AMPSystem_Permissions
 *
 *  a registry for allowed user actions
 *
 *  @version AMP 3.5.0
 *  @date 2005-07-02
 *  @author Austin Putman <austin@radicaldesigns.org>
 *
 * * * **/


class AMPSystem_PermissionManager {

    var $permission_array = array();
    var $userLevel;
    var $userid;
    var $_user;

    function AMPSystem_PermissionManager () {
    }

    function readLevel( $userLevel ) {
        if (!$allowed_permissions = &AMPSystemLookup_PermissionLevel::instance( $userLevel )) return false;
        $this->clear();
        $this->userLevel = $userLevel;

        foreach ($allowed_permissions as $permission_id ) {
            $this->allow( $permission_id );
        }
    }

    function &readUser( $userName ) {
        require_once( 'AMP/System/User/User.php');
        $user = &new AMPSystem_User( AMP_Registry::getDbcon( ) );
        $user->readUsername( $userName );
        if ( !$user->hasData( )) return false;
        $this->readLevel( $user->getPermissionGroup( ));
        $this->setUser( $user );
        return $user;
    }

    function getUser( ){
        if ( !isset( $this->_user )) return false;
        return $this->_user;
    }
        

    function clear() {
        $this->permission_array = array();
    }

    function setUser( &$user ) {
        $this->userid = $user->id;
        $this->_user = &$user;
    }

    function authorizedPage() {
        if ( !$this->_user->hasPageRestrictions( )) return true;

        $current_page = $_SERVER['PHP_SELF'];
        if ($url_values = AMP_URL_Values( )) $current_page .= join( "&", $url_values);
        if ( strpos( $current_page, '/system/') !== FALSE ) $current_page = substr( $current_page, 8 );

        return $this->_user->isAllowedPage( $current_page );

    }

    function userHome() {
        return $this->_user->getURLHome( );
    }


    function allow( $id ) {
        $this->permission_array[ $id ] = true;
    }

    function deny( $id ) {
        unset ($this->permission_array[ $id ]);
    }

    function authorized( $id ) {
        if (!isset( $this->permission_array[ $id ])) return false;
        return $this->permission_array[ $id ];
    }

    function &instance() {
        static $permissions = false;
        if (!$permissions) {
            $permissions =  new AMPSystem_PermissionManager();
        }
        return $permissions;
    }

    function entireSet() {
        return array_keys( $this->permission_array );
    }

    function getDescriptors() {
        return filterConstants( 'AMP_PERMISSION' );
    }

    function convertDescriptor( $desc ) {
        $new_desc = 'AMP_PERMISSION_' . $desc;
        if (!defined( $new_desc )) return null;
        return constant( $new_desc );
    }
        
}

?>
