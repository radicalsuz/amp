<?php

require_once( 'AMP/System/ComponentMap.inc.php');
define( 'AMP_MODULE_ID_LINKS', 11 );
define( 'AMP_INTROTEXT_ID_LINKS', 12 );

class ComponentMap_Link extends AMPSystem_ComponentMap {
    var $heading = "Link";
    var $nav_name = "links";

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
