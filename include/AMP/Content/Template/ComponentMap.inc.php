<?php

require_once( 'AMP/System/ComponentMap.inc.php');

class ComponentMap_Template extends AMPSystem_ComponentMap {
    var $heading = "Template";
    var $nav_name = "template";

    var $paths = array( 
        'fields' => 'AMP/Content/Template/Fields.xml',
        'list'   => 'AMP/Content/Template/List.inc.php',
        'form'   => 'AMP/Content/Template/Form.inc.php',
        'source' => 'AMP/Content/Template.inc.php');
    
    var $components = array( 
        'form'  => 'AMP_Content_Template_Form',
        'list'  => 'AMP_Content_Template_List',
        'source'=> 'AMPContent_Template');
}

?>
