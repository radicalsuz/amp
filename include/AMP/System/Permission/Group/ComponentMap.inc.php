<?php

require_once( 'AMP/System/ComponentMap.inc.php');

class ComponentMap_PermissionGroup extends AMPSystem_ComponentMap {

    var $heading = "Permission Group";
    var $nav_name = "system";

    var $_path_controller = 'AMP/System/Permission/Group/Controller.php';
    var $_component_controller = 'AMP_System_Permission_Group_Controller';
    var $_action_default = 'list';

    var $paths = array( 
        'fields' => 'AMP/System/Permission/Group/Fields.xml',
        'list'   => 'AMP/System/Permission/Group/List.inc.php',
        'form'   => 'AMP/System/Permission/Group/Form.inc.php',
        'source' => 'AMP/System/Permission/Group/Group.php'
        );
    
    var $components = array( 
        'form'  => 'PermissionGroup_Form',
        'list'  => 'PermissionGroup_List',
        'source'=> 'PermissionGroup');

    var $_allow_list  = AMP_PERMISSION_SYSTEM_PERMISSIONS ;
    var $_allow_edit  = AMP_PERMISSION_SYSTEM_PERMISSIONS ;
    var $_allow_add = AMP_PERMISSION_SYSTEM_PERMISSIONS ;
    var $_allow_save  = AMP_PERMISSION_SYSTEM_PERMISSIONS;
    var $_allow_publish   = AMP_PERMISSION_SYSTEM_PERMISSIONS;
    var $_allow_unpublish = AMP_PERMISSION_SYSTEM_PERMISSIONS;
    var $_allow_delete = AMP_PERMISSION_SYSTEM_PERMISSIONS;

    function onSave( &$controller  ) {
        ampredirect( AMP_SYSTEM_URL_PERMISSION_GROUP );
    }
}

?>
