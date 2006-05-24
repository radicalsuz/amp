<?php

require_once( 'AMP/System/ComponentMap.inc.php');

class ComponentMap_Setup extends AMPSystem_ComponentMap {
    var $heading = "Setup";
    var $nav_name = "system";
    var $_action_default = 'edit';
    var $_path_controller = 'AMP/System/Setup/Controller.php';
    var $_component_controller = 'AMP_System_Setup_Controller';

    var $paths = array( 
        'fields' => 'AMP/System/Setup/Fields.xml',
        'form'   => 'AMP/System/Setup/Form.inc.php',
        'source' => 'AMP/System/Setup/Setup.php');
    
    var $components = array( 
        'form'  => 'AMP_System_Setup_Form',
        'source'=> 'AMP_System_Setup');
}

?>
