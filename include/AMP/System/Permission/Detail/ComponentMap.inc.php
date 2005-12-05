<?php

require_once( 'AMP/System/ComponentMap.inc.php');

class ComponentMap_PermissionDetail extends AMPSystem_ComponentMap {
    var $heading = "Permission";
    var $nav_name = "system";

    var $paths = array( 
        'fields' => 'AMP/System/Permission/Detail/Fields.xml',
        'list'   => 'AMP/System/Permission/Detail/List.inc.php',
        'form'   => 'AMP/System/Permission/Detail/Form.inc.php',
        'source' => 'AMP/System/Permission/Detail/Detail.php');
    
    var $components = array( 
        'form'  => 'PermissionDetail_Form',
        'list'  => 'PermissionDetail_List',
        'source'=> 'PermissionDetail');
}

?>
