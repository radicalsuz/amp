<?php

require_once( 'AMP/System/ComponentMap.inc.php');
require_once( 'AMP/System/Permission/Observer/Section.php');

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

    var $_observers = array( 'AMP_System_Permission_Observer_Section');

    var $_allow_list = AMP_PERMISSION_LINKS_ACCESS ;
    var $_allow_edit = AMP_PERMISSION_LINKS_ACCESS ;
    var $_allow_save = AMP_PERMISSION_LINKS_ACCESS;
    var $_allow_publish = AMP_PERMISSION_LINKS_PUBLISH;
    var $_allow_unpublish = AMP_PERMISSION_LINKS_PUBLISH;
    var $_allow_delete = AMP_PERMISSION_LINKS_DELETE ;
}

?>
