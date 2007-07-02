<?php

require_once( 'AMP/System/ComponentMap.inc.php');

class ComponentMap_Template extends AMPSystem_ComponentMap {
    var $heading = "Template";
    var $nav_name = "template";
	var $_action_default = 'list';

    var $_component_controller = 'AMP_System_Component_Controller_Sticky';

    var $paths = array( 
        'fields' => 'AMP/Content/Template/Fields.xml',
        'list'   => 'AMP/Content/Template/List.inc.php',
        'form'   => 'AMP/Content/Template/Form.inc.php',
        'source' => 'AMP/Content/Template.inc.php');
    
    var $components = array( 
        'form'  => 'AMP_Content_Template_Form',
        'list'  => 'AMP_Content_Template_List',
        'source'=> 'AMPContent_Template');

    var $_allow_list = AMP_PERMISSION_CONTENT_TEMPLATE ;
    var $_allow_edit = AMP_PERMISSION_CONTENT_TEMPLATE ;
    var $_allow_save = AMP_PERMISSION_CONTENT_TEMPLATE;
    var $_allow_publish = AMP_PERMISSION_CONTENT_TEMPLATE;
    var $_allow_unpublish = AMP_PERMISSION_CONTENT_TEMPLATE;
    var $_allow_delete = AMP_PERMISSION_CONTENT_TEMPLATE;

    function onBeforeUpdate( &$controller, $args = array( ) ){
        if ( isset( $args['model'])) {
            $args['model']->save_version( );
        }
    }
}

?>
