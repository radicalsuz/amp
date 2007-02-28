<?php

require_once( 'AMP/System/ComponentMap.inc.php');
if ( !defined( 'AMP_FORM_ID_WEBACTION_DEFAULT')) define( 'AMP_FORM_ID_WEBACTION_DEFAULT', 12 );

class ComponentMap_WebAction extends AMPSystem_ComponentMap {
    var $heading = "Web Action";
    var $nav_name = "actions";
    var $_action_default = 'list';

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

    var $_allow_list = AMP_PERMISSION_ACTION_ACCESS ;
    var $_allow_edit = AMP_PERMISSION_ACTION_ACCESS ;
    var $_allow_save = AMP_PERMISSION_ACTION_ADMIN;
    var $_allow_publish = AMP_PERMISSION_ACTION_PUBLISH;
    var $_allow_unpublish = AMP_PERMISSION_ACTION_PUBLISH;
    var $_allow_delete = AMP_PERMISSION_ACTION_DELETE;
}
?>
