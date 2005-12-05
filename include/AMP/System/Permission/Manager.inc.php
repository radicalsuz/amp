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
    var $usersettings = array(
        'allowed' => array(),
        'home' => 'index.php'
        );
    var $userid;

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

    function readUser(&$dbcon, $userID ) {
        if (!$uservals = $dbcon->GetAll("Select id, system_allow_only, system_home from users where id=" . $dbcon->qstr( $userID ))) return false;
        $this->setUser( $uservals );
    }
        

    function clear() {
        $this->permission_array = array();
    }

    function setUser( $data ) {
        $this->userid = $data['id'];
        $this->usersettings = array(
            'allowed' => array(),
            'home' => 'index.php');
        if ($data['system_allow_only']) $this->usersettings['allowed'] = split("[ ]?,[ ]?", $data['system_allow_only']);
        if ($data['system_home']) $this->usersettings['home'] = $data['system'];
    }

    function authorizedPage() {
        if (empty($this->usersettings['allowed'])) return true;

        $current_page = $_SERVER['PHP_SELF'];
        if ($url = AMP_URL_Values) $current_page .= join( "&", $url);

        if (array_search( $current_page, $this->usersettings['allowed'] )===FALSE) return false;
        return true;
    }

    function userHome() {
        return $this->usersettings['home'];
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
