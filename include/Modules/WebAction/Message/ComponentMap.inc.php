<?php

require_once( 'AMP/System/ComponentMap.inc.php');

class ComponentMap_WebActionMessage extends AMPSystem_ComponentMap {
    var $heading = 'Web Action Message';
    var $nav_name = 'action';

    var $paths = array( 
        'fields' => 'Modules/WebAction/Message/Fields.xml',
        'list'   => 'Modules/WebAction/Message/List.inc.php',
        'form'   => 'Modules/WebAction/Message/Form.inc.php',
        'source' => 'Modules/WebAction/Message/Message.php');

    var $components = array( 
        'form'  => 'WebActionMessage_Form',
        'list'  => 'WebActionMessage_List',
        'source'=> 'WebActionMessage');
}
?>
