<?php

require_once( 'AMP/System/ComponentMap.inc.php');
if ( !defined( 'AMP_FORM_ID_WEBACTION_DEFAULT')) define( 'AMP_FORM_ID_WEBACTION_DEFAULT', 12 );

class ComponentMap_WebAction extends AMPSystem_ComponentMap {
    var $heading = "Web Action";
    var $nav_name = "action";

    var $paths = array( 
        'fields' => 'Modules/WebAction/Fields.xml',
        'list'   => 'Modules/WebAction/List.inc.php',
        'form'   => 'Modules/WebAction/Form.inc.php',
        'source' => 'Modules/WebAction/WebAction.php');

    var $components = array( 
        'form'  => 'WebAction_Form',
        'list'  => 'WebAction_List',
        'source'=> 'WebAction');

    var $_path_controller = 'Modules/WebAction/Controller.inc.php';
    var $_component_controller = 'WebAction_Controller';

}
?>
