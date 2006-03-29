<?php

require_once( 'AMP/System/ComponentMap.inc.php');

class ComponentMap_Class extends AMPSystem_ComponentMap {
    var $heading = "Class";
    var $nav_name = "class";

    var $paths = array( 
        'fields' => 'AMP/Content/Class/Fields.xml',
        'list'   => 'AMP/Content/Class/List.inc.php',
        'form'   => 'AMP/Content/Class/Form.inc.php',
        'source' => 'AMP/Content/Class.inc.php');
    
    var $components = array( 
        'form'  => 'Class_Form',
        'list'  => 'Class_List',
        'source'=> 'ContentClass');
}

?>
