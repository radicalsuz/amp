<?php

require_once( 'AMP/System/ComponentMap.inc.php');

class ComponentMap_Stylesheet extends AMPSystem_ComponentMap {
    var $heading = "Stylesheet";
    var $nav_name = "template";

    var $_component_controller = 'AMP_System_Component_Controller_Sticky';

    var $paths = array( 
        'fields' => 'AMP/Content/Stylesheet/Fields.xml',
        'list'   => 'AMP/Content/Stylesheet/List.inc.php',
        'form'   => 'AMP/Content/Stylesheet/Form.inc.php',
        'source' => 'AMP/Content/Stylesheet/Stylesheet.php' 
        );
    
    var $components = array( 
        'form'  => 'AMP_Content_Stylesheet_Form',
        'list'  => 'AMP_Content_Stylesheet_List',
        'source'=> 'AMP_Content_Stylesheet');

    var $_action_default = 'list';

    var $_allow_list = AMP_PERMISSION_CONTENT_CSS ;
    var $_allow_edit = AMP_PERMISSION_CONTENT_CSS ;
    var $_allow_save = AMP_PERMISSION_CONTENT_CSS;
    var $_allow_publish = AMP_PERMISSION_CONTENT_CSS;
    var $_allow_unpublish = AMP_PERMISSION_CONTENT_CSS;
    var $_allow_delete = AMP_PERMISSION_CONTENT_CSS;
}

?>
