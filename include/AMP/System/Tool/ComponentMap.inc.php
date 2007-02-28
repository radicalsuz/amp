<?php

require_once( 'AMP/System/ComponentMap.inc.php');

class ComponentMap_Tool extends AMPSystem_ComponentMap {
    var $heading = "Tool";
    var $nav_name = "tools";
    var $_action_default = 'list';

    var $paths = array( 
        'fields' => 'AMP/System/Tool/Fields.xml',
        'list'   => 'AMP/System/Tool/List.inc.php',
        'form'   => 'AMP/System/Tool/Form.inc.php',
        'source' => 'AMP/System/Tool.inc.php');
    
    var $components = array( 
        'form'  => 'Tool_Form',
        'list'  => 'Tool_List',
        'source'=> 'AMPSystem_Tool');

    var $_allow_list = AMP_PERMISSION_TOOLS_ACCESS;
    var $_allow_edit = AMP_PERMISSION_TOOLS_ADMIN ;
    var $_allow_save = AMP_PERMISSION_TOOLS_ADMIN;
    var $_allow_publish = AMP_PERMISSION_TOOLS_ADMIN;
    var $_allow_unpublish = AMP_PERMISSION_TOOLS_ADMIN;
    var $_allow_delete = AMP_PERMISSION_TOOLS_ADMIN;
}

?>
