<?php

require_once( 'AMP/System/ComponentMap.inc.php');

class ComponentMap_Nav_Location extends AMPSystem_ComponentMap {
    var $heading = "Navigation Location";
    var $nav_name = "nav";
    var $_action_default = 'list';

    var $paths = array( 
        'fields' => 'AMP/Content/Nav/Location/Fields.xml',
        'list'   => 'AMP/Content/Nav/Location/List.inc.php',
        'form'   => 'AMP/Content/Nav/Location/Form.inc.php',
        'source' => 'AMP/Content/Nav/Location/Location.php');
    
    var $components = array( 
        'form'  => 'AMP_Content_Nav_Location_Form',
        'list'  => 'AMP_Content_Nav_Location_List',
        'source'=> 'AMP_Content_Nav_Location');
}

?>
