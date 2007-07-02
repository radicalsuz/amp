<?php

require_once( 'AMP/System/ComponentMap.inc.php');

class ComponentMap_Template_Archive extends AMPSystem_ComponentMap {
    var $heading = "Template Archive";
    var $nav_name = "template";
	var $_action_default = 'list';

    var $paths = array( 
        'fields' => 'AMP/Content/Template/Fields.xml',
        'list'   => 'AMP/Content/Template/Archive/List.php',
        'form'   => 'AMP/Content/Template/Archive/Form.php',
        'source' => 'AMP/Content/Template/Archive/Archive.php');
    
    var $components = array( 
        'form'  => 'AMP_Content_Template_Archive_Form',
        'list'  => 'AMP_Content_Template_Archive_List',
        'source'=> 'AMP_Content_Template_Archive');

    var $_allow_list = AMP_PERMISSION_CONTENT_TEMPLATE ;
    var $_allow_edit = AMP_PERMISSION_CONTENT_TEMPLATE ;
    var $_allow_save = AMP_PERMISSION_CONTENT_TEMPLATE;
    var $_allow_publish = AMP_PERMISSION_CONTENT_TEMPLATE;
    var $_allow_unpublish = AMP_PERMISSION_CONTENT_TEMPLATE;
    var $_allow_delete = AMP_PERMISSION_CONTENT_TEMPLATE;
}

?>
