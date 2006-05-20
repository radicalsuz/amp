<?php

require_once( 'AMP/System/ComponentMap.inc.php');

class ComponentMap_FAQ extends AMPSystem_ComponentMap {
    var $heading = "FAQ";
    var $nav_name = "faq";

    var $paths = array( 
        'fields' => 'Modules/FAQ/Fields.xml',
        'list'   => 'Modules/FAQ/List.inc.php',
        'form'   => 'Modules/FAQ/Form.inc.php',
        'source' => 'Modules/FAQ/FAQ.php');
    
    var $components = array( 
        'form'  => 'FAQ_Form',
        'list'  => 'FAQ_List',
        'source'=> 'FAQ');
}

?>
