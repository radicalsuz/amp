<?php

require_once( 'AMP/System/ComponentMap.inc.php');

class ComponentMap_%1\$s extends AMPSystem_ComponentMap {
    var $heading = "%1\$s";
    var $nav_name = "%2\$s";
	var $_action_default = 'list';

    var $paths = array( 
        'fields' => '%4\$s%1\$s/Fields.xml',
        'list'   => '%4\$s%1\$s/List.inc.php',
        'form'   => '%4\$s%1\$s/Form.inc.php',
        'source' => '%4\$s%1\$s/%1\$s.php');
    
    var $components = array( 
        'form'  => '%1\$s_Form',
        'list'  => '%1\$s_List',
        'source'=> '%1\$s');
}

?>
