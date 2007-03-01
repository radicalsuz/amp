<?php

require_once( 'AMP/System/ComponentMap.inc.php' );

class ComponentMap_WebAction_Public extends AMPSystem_ComponentMap {
    var $_action_default = 'add';
    var $_path_controller = 'Modules/WebAction/Public/Controller.php';
    var $_component_controller = 'WebAction_Public_Component_Controller';
    var $_public_page_id_input = AMP_CONTENT_PUBLICPAGE_ID_WEBACTION_INPUT;

    var $paths = array(
        'fields' => 'Modules/WebAction/Public/Fields.xml',
        'form'          => 'Modules/WebAction/Public/Form.inc.php',
        'source' => 'Modules/WebAction/WebAction.php',
        'list'  =>  'Modules/WebAction/Public/List.inc.php'
        );

    var $components = array (
        'form' => 'WebAction_Public_Form',
        'list'  =>  'WebAction_Public_List',
        'source' => 'WebAction' 
        );
}

?>
