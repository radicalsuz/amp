<?php

require_once( 'AMP/System/ComponentMap.inc.php');

class ComponentMap_ToolControl extends AMPSystem_ComponentMap {
    var $heading = "Tool Control";
    var $nav_name = "tools";

    var $paths = array( 
        'fields' => 'AMP/System/Tool/Control/Fields.xml',
        'list'   => 'AMP/System/Tool/Control/List.inc.php',
        'form'   => 'AMP/System/Tool/Control/Form.inc.php',
        'source' => 'AMP/System/Tool/Control/Control.php');
    
    var $components = array( 
        'form'  => 'ToolControl_Form',
        'list'  => 'ToolControl_List',
        'source'=> 'ToolControl');

    var $_allow_list = AMP_PERMISSION_TOOLS_ADMIN ;
    var $_allow_edit = AMP_PERMISSION_TOOLS_ADMIN ;
    var $_allow_save = AMP_PERMISSION_TOOLS_ADMIN;
    var $_allow_publish = AMP_PERMISSION_TOOLS_ADMIN;
    var $_allow_unpublish = AMP_PERMISSION_TOOLS_ADMIN;
    var $_allow_delete = AMP_PERMISSION_TOOLS_ADMIN;
}

?>
