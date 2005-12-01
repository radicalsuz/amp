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
}

?>
