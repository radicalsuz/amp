<?php

require_once( 'AMP/System/ComponentMap.inc.php');

class ComponentMap_Tool extends AMPSystem_ComponentMap {
    var $heading = "Tool";
    var $nav_name = "tools";

    var $paths = array( 
        'fields' => 'AMP/System/Tool/Fields.xml',
        'list'   => 'AMP/System/Tool/List.inc.php',
        'form'   => 'AMP/System/Tool/Form.inc.php',
        'source' => 'AMP/System/Tool.inc.php');
    
    var $components = array( 
        'form'  => 'Tool_Form',
        'list'  => 'Tool_List',
        'source'=> 'AMPSystem_Tool');
}

?>
