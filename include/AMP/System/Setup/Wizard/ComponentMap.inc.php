<?php

require_once( 'AMP/System/ComponentMap.inc.php');

class ComponentMap_Setup_Wizard extends AMPSystem_ComponentMap {
    var $heading = "Setup Wizard";
    var $nav_name = "system";

    var $_path_controller = 'AMP/System/Setup/Wizard/Controller.php';
    var $_component_controller = 'AMP_System_Setup_Wizard_Controller';

    var $_action_default = 'edit';

    var $paths = array( 
        'fields' => 'AMP/System/Setup/Wizard/Fields.xml',
        'form'   => 'AMP/System/Setup/Form.inc.php',
        'source' => 'AMP/System/Setup/Setup.php');
    
    var $components = array( 
        'form'  => 'AMP_System_Setup_Form',
        'source'=> 'AMP_System_Setup');
}
?>
