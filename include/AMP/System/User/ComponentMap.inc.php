<?php

require_once( 'AMP/System/ComponentMap.inc.php');

class ComponentMap_User extends AMPSystem_ComponentMap {
    var $heading = "User";
    var $nav_name = "system";
    var $_action_default = 'list';

    var $paths = array( 
        'fields' => 'AMP/System/User/Fields.xml',
        'list'   => 'AMP/System/User/List.inc.php',
        'form'   => 'AMP/System/User/Form.inc.php',
        'source' => 'AMP/System/User/User.php');
    
    var $components = array( 
        'form'  => 'User_Form',
        'list'  => 'User_List',
        'source'=> 'AMPSystem_User');
    var $_path_controller = 'AMP/System/User/Controller.php';
    var $_component_controller = 'AMP_System_User_Controller';

    var $_allow_list = AMP_PERMISSION_SYSTEM_USERS ;
    var $_allow_edit = AMP_PERMISSION_SYSTEM_USERS ;
    var $_allow_save = AMP_PERMISSION_SYSTEM_USERS;
    var $_allow_publish = AMP_PERMISSION_SYSTEM_USERS;
    var $_allow_unpublish = AMP_PERMISSION_SYSTEM_USERS;
    var $_allow_delete = AMP_PERMISSION_SYSTEM_USERS;

}

?>
