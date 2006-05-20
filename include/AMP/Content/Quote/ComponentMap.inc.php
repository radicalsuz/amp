<?php

require_once( 'AMP/System/ComponentMap.inc.php');

class ComponentMap_Quote extends AMPSystem_ComponentMap {
    var $heading = "Quote";
    var $nav_name = "content";

    var $paths = array( 
        'fields' => 'AMP/Content/Quote/Fields.xml',
        'list'   => 'AMP/Content/Quote/List.inc.php',
        'form'   => 'AMP/Content/Quote/Form.inc.php',
        'source' => 'AMP/Content/Quote/Quote.php');
    
    var $components = array( 
        'form'  => 'Quote_Form',
        'list'  => 'Quote_List',
        'source'=> 'Quote');
}

?>
