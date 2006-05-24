<?php
require_once( 'AMP/System/ComponentMap.inc.php');

class ComponentMap_AMP_System_UserData extends AMPSystem_ComponentMap {

    var $heading = "Form";
    var $nav_name = "content";
    var $_action_default = 'list';
    var $_path_controller = 'AMP/System/UserData/Controller.php';
    var $_component_controller = 'AMP_System_UserData_Controller';

    var $paths = array( 
        'form'   => 'AMP/System/UserData/Form.inc.php',
        'list'   => 'AMP/System/UserData/List.inc.php',
        'fields' => 'AMP/System/UserData/Fields.xml',
        'source' => 'AMP/System/UserData.php' 
    );

    var $components = array ( 
        'form'  => 'AMP_System_UserData_Form',
        'list'  => 'AMP_System_UserData_List',
        'source' => 'AMPSystem_UserData'
    );
}

?>
