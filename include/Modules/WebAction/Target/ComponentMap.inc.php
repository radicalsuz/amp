<?php

require_once( 'AMP/System/ComponentMap.inc.php');

class ComponentMap_WebAction_Target extends AMPSystem_ComponentMap {
    var $heading = "WebAction Target";
    var $nav_name = "webaction_targets";

    var $paths = array( 
        'fields' => 'Modules/WebAction/Target/Fields.xml',
        'list'   => 'Modules/WebAction/Target/List.inc.php',
        'form'   => 'Modules/WebAction/Target/Form.inc.php',
        'source' => 'Modules/WebAction/Target/Target.php');
    
    var $components = array( 
        'form'  => 'WebAction_Target_Form',
        'list'  => 'WebAction_Target_List',
        'source'=> 'WebAction_Target');
}

?>
