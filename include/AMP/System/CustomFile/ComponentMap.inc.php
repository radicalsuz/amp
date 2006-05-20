<?php

require_once( 'AMP/System/ComponentMap.inc.php');

class ComponentMap_CustomFile extends AMPSystem_ComponentMap {
    var $heading = "Custom File";
    var $nav_name = "template";

    var $paths = array( 
        'fields' => 'AMP/System/CustomFile/Fields.xml',
        'list'   => 'AMP/System/CustomFile/List.inc.php',
        'form'   => 'AMP/System/CustomFile/Form.inc.php',
        'source' => 'AMP/System/CustomFile/CustomFile.php' 
        );
    
    var $components = array( 
        'form'  => 'AMP_System_CustomFile_Form',
        'list'  => 'AMP_System_CustomFile_List',
        'source'=> 'AMP_System_CustomFile');

    var $_action_default = 'list';
}

?>
