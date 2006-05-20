<?php

require_once( 'AMP/System/ComponentMap.inc.php');

class ComponentMap_Hotword extends AMPSystem_ComponentMap {
    var $heading = "Hotword";
    var $nav_name = "content";

    var $paths = array( 
        'fields' => 'AMP/Content/Hotword/Fields.xml',
        'list'   => 'AMP/Content/Hotword/List.inc.php',
        'form'   => 'AMP/Content/Hotword/Form.inc.php',
        'source' => 'AMP/Content/Hotword/Hotword.php');
    
    var $components = array( 
        'form'  => 'Hotword_Form',
        'list'  => 'Hotword_List',
        'source'=> 'Hotword');
}

?>
