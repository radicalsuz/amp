<?php

require_once( 'AMP/System/ComponentMap.inc.php');

class ComponentMap_FAQ_Type extends AMPSystem_ComponentMap {
    var $heading = "FAQ Type";
    var $nav_name = "faq";

    var $paths = array( 
        'fields' => 'Modules/FAQ/Type/Fields.xml',
        'list'   => 'Modules/FAQ/Type/List.inc.php',
        'form'   => 'Modules/FAQ/Type/Form.inc.php',
        'source' => 'Modules/FAQ/Type/Type.php');
    
    var $components = array( 
        'form'  => 'FAQ_Type_Form',
        'list'  => 'FAQ_Type_List',
        'source'=> 'FAQ_Type');
}

?>
