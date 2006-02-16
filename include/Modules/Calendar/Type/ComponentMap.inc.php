<?php

require_once( 'AMP/System/ComponentMap.inc.php');

class ComponentMap_Calendar_Type extends AMPSystem_ComponentMap {
    var $heading = "Event Type";
    var $nav_name = "calendar";

    var $paths = array( 
        'fields' => 'Modules/Calendar/Type/Fields.xml',
        'list'   => 'Modules/Calendar/Type/List.inc.php',
        'form'   => 'Modules/Calendar/Type/Form.inc.php',
        'source' => 'Modules/Calendar/Type/Type.php');
    
    var $components = array( 
        'form'  => 'Calendar_Type_Form',
        'list'  => 'Calendar_Type_List',
        'source'=> 'Calendar_Type');
}

?>
