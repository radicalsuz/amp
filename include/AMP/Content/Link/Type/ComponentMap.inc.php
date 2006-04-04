<?php

require_once( 'AMP/System/ComponentMap.inc.php');

class ComponentMap_Link_Type extends AMPSystem_ComponentMap {
    var $heading = "Link Type";
    var $nav_name = "links";

    var $paths = array( 
        'fields' => 'AMP/Content/Link/Type/Fields.xml',
        'list'   => 'AMP/Content/Link/Type/List.inc.php',
        'form'   => 'AMP/Content/Link/Type/Form.inc.php',
        'source' => 'AMP/Content/Link/Type/Type.php');
    
    var $components = array( 
        'form'  => 'Link_Type_Form',
        'list'  => 'Link_Type_List',
        'source'=> 'Link_Type');
}

?>
