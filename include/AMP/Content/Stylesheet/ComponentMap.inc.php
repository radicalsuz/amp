<?php

require_once( 'AMP/System/ComponentMap.inc.php');

class ComponentMap_Stylesheet extends AMPSystem_ComponentMap {
    var $heading = "Stylesheet";
    var $nav_name = "template";

    var $paths = array( 
        'fields' => 'AMP/Content/Stylesheet/Fields.xml',
        'list'   => 'AMP/Content/Stylesheet/List.inc.php',
        'form'   => 'AMP/Content/Stylesheet/Form.inc.php',
        'source' => 'AMP/Content/Stylesheet/Stylesheet.php' 
        );
    
    var $components = array( 
        'form'  => 'AMP_Content_Stylesheet_Form',
        'list'  => 'AMP_Content_Stylesheet_List',
        'source'=> 'AMP_Content_Stylesheet');

    var $_action_default = 'list';
}

?>
