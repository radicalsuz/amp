<?php

require_once( 'AMP/System/ComponentMap.inc.php');

class ComponentMap_Badge extends AMPSystem_ComponentMap {
    var $heading = "Badge";
    var $nav_name = "badges";
	var $_action_default = 'list';

    var $paths = array( 
        'fields' => 'AMP/Content/Badge/Fields.xml',
        'list'   => 'AMP/Content/Badge/List.inc.php',
        'form'   => 'AMP/Content/Badge/Form.inc.php',
        'source' => 'AMP/Content/Badge/Badge.php');
    
    var $components = array( 
        'form'  => 'AMP_Content_Badge_Form',
        'list'  => 'AMP_Content_Badge_List',
        'source'=> 'AMP_Content_Badge');
}

?>
