<?php

require_once( 'AMP/System/ComponentMap.inc.php');

class ComponentMap_Link extends AMPSystem_ComponentMap {
    var $heading = "Link";
    var $nav_name = "links";
    var $_action_default = 'list';

    var $paths = array( 
        'fields' => 'AMP/Content/Link/Fields.xml',
        'list'   => 'AMP/Content/Link/List.inc.php',
        'form'   => 'AMP/Content/Link/Form.inc.php',
        'display'   => 'AMP/Content/Link/Display.php',
        'source' => 'AMP/Content/Link/Link.php');
    
    var $components = array( 
        'form'      => 'AMP_Content_Link_Form',
        'list'      => 'AMP_Content_Link_List',
        'display'   => 'AMP_Content_Link_Display',
        'source'    => 'AMP_Content_Link' ) ;

}

?>
