<?php
define('AMP_PERMISSION_CONTENT_ACCESS', 73);

define('AMP_PERMISSION_CONTENT_EDIT', 1);
define('AMP_PERMISSION_CONTENT_DELETE', 2);
define('AMP_PERMISSION_CONTENT_PUBLISH', 98);

define('AMP_PERMISSION_CONTENT_SECTION_LIMIT', 97);
define('AMP_PERMISSION_CONTENT_SECTION_DELETE', 6);
define('AMP_PERMISSION_CONTENT_SECTION_EDIT', 9);
define('AMP_PERMISSION_CONTENT_CLASS_EDIT', 8);
define('AMP_PERMISSION_CONTENT_CLASS_DELETE', 7);

define('AMP_PERMISSION_CONTENT_DOCUMENTS_EDIT', 85);
define('AMP_PERMISSION_CONTENT_IMAGES_EDIT', 85);
define('AMP_PERMISSION_CONTENT_FILES_ACCESS', 85);

define('AMP_PERMISSION_CONTENT_RSS_ACCESS', 102);
define('AMP_PERMISSION_CONTENT_RSS_AGGREGATOR', 102);
define('AMP_PERMISSION_CONTENT_RSS_PUBLISH', 300);

define('AMP_PERMISSION_CONTENT_NAVIGATION', 47);
define('AMP_PERMISSION_CONTENT_TEMPLATE', 49);
define('AMP_PERMISSION_CONTENT_CSS', 48);

define('AMP_PERMISSION_CALENDAR_ACCESS', 11);
define('AMP_PERMISSION_CALENDAR_PUBLISH', 12);
define('AMP_PERMISSION_CALENDAR_DELETE', 13);

define('AMP_PERMISSION_FAQ_ACCESS', 22);
define('AMP_PERMISSION_FAQ_PUBLISH', 24);
define('AMP_PERMISSION_FAQ_DELETE', 23);

define('AMP_PERMISSION_LINKS_ACCESS', 26);
define('AMP_PERMISSION_LINKS_PUBLISH', 28);
define('AMP_PERMISSION_LINKS_DELETE', 27);

define('AMP_PERMISSION_ACTION_ADMIN', 33);
define('AMP_PERMISSION_ACTION_ACCESS', 31);
define('AMP_PERMISSION_ACTION_PUBLISH', 30);
define('AMP_PERMISSION_ACTION_DELETE', 32);

define('AMP_PERMISSION_GALLERY_ADMIN', 37);
define('AMP_PERMISSION_GALLERY_ACCESS', 34);
define('AMP_PERMISSION_GALLERY_IMAGE_PUBLISH', 36);
define('AMP_PERMISSION_GALLERY_IMAGE_DELETE', 35);

define('AMP_PERMISSION_FORM_ADMIN', 53); 

define('AMP_PERMISSION_FORM_DATA_EDIT', 54);
define('AMP_PERMISSION_FORM_DATA_PUBLISH', 55);
define('AMP_PERMISSION_FORM_DATA_EMAIL', 301);
define('AMP_PERMISSION_FORM_DATA_EXPORT',302);

define('AMP_PERMISSION_BLAST_ACCESS', 38);
define('AMP_PERMISSION_BLAST_ADMIN', 39);
define('AMP_PERMISSION_BLAST_SEND', 40);

define('AMP_PERMISSION_SYSTEM_ACCESS', 306);
define('AMP_PERMISSION_SYSTEM_SETTINGS', 52);
define('AMP_PERMISSION_SYSTEM_USERS', 51);
define('AMP_PERMISSION_SYSTEM_PERMISSIONS', 50);

define('AMP_PERMISSION_TOOLS_ACCESS', 305);
define('AMP_PERMISSION_TOOLS_ADMIN', 306);
define('AMP_PERMISSION_TOOLS_INTROTEXT', 45);
define('AMP_PERMISSION_TOOLS_CUSTOMFILES', 46);

define('AMP_PERMISSION_CONTACT_ADMIN', 65);
define('AMP_PERMISSION_CONTACT_CAMPAIGN', 66);
define('AMP_PERMISSION_CONTACT_REGION', 67);
define('AMP_PERMISSION_CONTACT_TYPE', 68);
define('AMP_PERMISSION_CONTACT_SOURCE', 69);
define('AMP_PERMISSION_CONTACT_USER', 70);
define('AMP_PERMISSION_CONTACT_OUTLOOK', 71);
define('AMP_PERMISSION_CONTACT_ACCESS', 72);
define('AMP_PERMISSION_CONTACT_EDIT', 95);
define('AMP_PERMISSION_CONTACT_DELETE', 95);

define('AMP_PERMISSION_PETITION_ADMIN', 74);

define('AMP_PERMISSION_MESSAGES_ACCESS', 89);
define('AMP_PERMISSION_MESSAGES_ADMIN', 90);

define('AMP_PERMISSION_QUOTES_ACCESS', 99);

define('AMP_PERMISSION_FORM_MEDIA', 18);
define('AMP_PERMISSION_FORM_BOARD_RIDE', 41);
define('AMP_PERMISSION_FORM_BOARD_HOUSING', 42);
define('AMP_PERMISSION_FORM_GROUPS', 56);
define('AMP_PERMISSION_FORM_ENDORSE', 59);
define('AMP_PERMISSION_FORM_TRAINER', 78);
define('AMP_PERMISSION_FORM_SPEAKER', 81);
define('AMP_PERMISSION_FORM_VOLUNTEER', 84);
define('AMP_PERMISSION_FORM_PETITION', 75 );

define('AMP_PERMISSION_PAYMENT_ACCESS', 303);
define('AMP_PERMISSION_PAYMENT_ADMIN', 304);

define('AMP_PERMISSION_TOOLS_DIRECTORIES_ACCESS', 56);
define('AMP_PERMISSION_TOOLS_BOARDS_ACCESS', 42);
define('AMP_PERMISSION_TOOLS_CUSTOM_ACCESS', 54);
define('AMP_PERMISSION_TOOLS_ADVOCACY_ACCESS', 59);

define('AMP_PERMISSION_CONTENT_TOOLS_ACCESS', 9);
define('AMP_PERMISSION_FORM_ACCESS', 54);
define('AMP_PERMISSION_CONTENT_USER_ADMIN', 54);
define('AMP_PERMISSION_CONTENT_USER_ADDED_CONTENT', 98);


/* * * * * * *
 *  AMPSystem_Permissions
 *
 *  a registry for allowed user actions
 *
 *  AMP 3.5.0
 *
 *  2005-07-02
 *  Author: austin@radicaldesigns.org
 *
 * * * **/

//a quick-use function to check a single permission value

function AMP_Authorized( $id ) {
    static $permissions = false;
    if ( !$permissions ) $permissions = & AMPSystem_PermissionManager::instance();
    return $permissions->authorized ($id);
}


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

    function readLevel(&$dbcon, $userLevel ) {
        $sql = "SELECT id, perid FROM permission WHERE groupid = ". $dbcon->qstr( $userLevel );
        if (!$valper = $dbcon->GetAll($sql)) return false;
        $this->clear();
        $this->userLevel = $userLevel;

        foreach ($valper as $perdef) {
            $this->allow( $perdef['perid'] );
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
		/*
        $constant_set = get_defined_constants();
        $per_flag =  'AMP_PERMISSION_';
        $descriptor_set = array();
        foreach ($constant_set as $name => $value ) {
            if (strpos( $name, $per_flag )!==0) continue;

            $desc = substr( $name, strlen($per_flag) );
            $descriptor_set[$desc] = $value;
        }
		*/

        return filterConstants( 'AMP_PERMISSION' );
    }

    function convertDescriptor( $desc ) {
        $new_desc = 'AMP_PERMISSION_' . $desc;
        if (!defined( $new_desc )) return null;
        return constant( $new_desc );
    }
        
}
?>
