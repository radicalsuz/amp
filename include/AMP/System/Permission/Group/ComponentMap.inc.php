<?php

require_once( 'AMP/System/ComponentMap.inc.php');

class ComponentMap_PermissionGroup extends AMPSystem_ComponentMap {
    var $heading = "Permission Group";
    var $nav_name = "system";

    var $paths = array( 
        'fields' => 'AMP/System/Permission/Group/Fields.xml',
        'list'   => 'AMP/System/Permission/Group/List.inc.php',
        'form'   => 'AMP/System/Permission/Group/Form.inc.php',
        'source' => 'AMP/System/Permission/Group/Group.php');
    
    var $components = array( 
        'form'  => 'PermissionGroup_Form',
        'list'  => 'PermissionGroup_List',
        'source'=> 'PermissionGroup');
}

?>
