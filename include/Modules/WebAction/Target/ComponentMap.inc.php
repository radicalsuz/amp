<?php

require_once( 'AMP/System/ComponentMap.inc.php');

class ComponentMap_WebAction_Target extends AMPSystem_ComponentMap {
    var $heading = "WebAction Target";
    var $nav_name = "actions";
    var $_action_default = 'list';

    var $paths = array( 
        'fields' => 'Modules/WebAction/Target/Fields.xml',
        'list'   => 'Modules/WebAction/Target/List.inc.php',
        'form'   => 'Modules/WebAction/Target/Form.inc.php',
        'source' => 'Modules/WebAction/Target/Target.php');
    
    var $components = array( 
        'form'  => 'WebAction_Target_Form',
        'list'  => 'WebAction_Target_List',
        'source'=> 'WebAction_Target');

    var $_allow_list = AMP_PERMISSION_ACTION_ACCESS;
    var $_allow_edit = AMP_PERMISSION_ACTION_ADMIN ;
    var $_allow_save = AMP_PERMISSION_ACTION_ADMIN;
    var $_allow_publish = AMP_PERMISSION_ACTION_PUBLISH;
    var $_allow_unpublish = AMP_PERMISSION_ACTION_PUBLISH;
    var $_allow_delete = AMP_PERMISSION_ACTION_DELETE;
}

?>
