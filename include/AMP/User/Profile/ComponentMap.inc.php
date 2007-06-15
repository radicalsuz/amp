<?php

require_once( 'AMP/System/ComponentMap.inc.php');

class ComponentMap_User_Profile extends AMPSystem_ComponentMap {
    var $heading = "User";
    var $nav_name = "system";

    var $_action_default = 'list';

    var $paths = array( 
        'fields' => 'AMP/User/Profile/Fields.xml',
        'list'   => 'AMP/User/Profile/List.php',
        'form'   => 'AMP/User/Profile/Form.inc.php',
        'source' => 'AMP/User/Profile/Profile.php',
        'search' => 'AMP/User/Profile/Search/Form.php',
        'search_fields' => 'AMP/User/Profile/Search/Fields.xml',
        );
    
    var $components = array( 
        'form'  => 'AMP_User_Profile_Form',
        'list'  => 'AMP_User_Profile_List',
        'search' => 'AMP_User_Profile_Search_Form',
        'source'=> 'AMP_User_Profile',
        );

    var $_allow_list = AMP_PERMISSION_FORM_DATA_EDIT;
    var $_allow_edit = AMP_PERMISSION_FORM_DATA_EDIT;
    var $_allow_save = AMP_PERMISSION_FORM_DATA_EDIT;
    var $_allow_publish = AMP_PERMISSION_FORM_DATA_PUBLISH;
    var $_allow_unpublish = AMP_PERMISSION_FORM_DATA_PUBLISH;
    var $_allow_delete = AMP_PERMISSION_FORM_DATA_EDIT;
    var $_allow_export = AMP_PERMISSION_FORM_DATA_EXPORT;

    var $_allow_search = AMP_PERMISSION_FORM_DATA_EDIT;

    function isAllowed( $action, $id = false ) {
        if ( ( $action == 'subscribe') && ( !( AMP_MODULE_BLAST ) || ( AMP_MODULE_BLAST == 'AMP' ))) {
            return false;
        }
        return parent::isAllowed( $action, $id );

    }
}

?>
