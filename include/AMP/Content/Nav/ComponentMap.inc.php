<?php

require_once( 'AMP/System/ComponentMap.inc.php');
require_once( 'AMP/System/Permission/Observer/Tool.php' );

class ComponentMap_Nav extends AMPSystem_ComponentMap {
    var $heading = "Navigation File";
    var $nav_name = "nav";
    var $_action_default = 'list';

    var $paths = array( 
        'fields' => 'AMP/Content/Nav/Fields.xml',
        'list'   => 'AMP/Content/Nav/List.inc.php',
        'form'   => 'AMP/Content/Nav/Form.inc.php',
        'source' => 'AMP/Content/Nav.inc.php');
    
    var $components = array( 
        'form'  => 'Nav_Form',
        'list'  => 'Nav_List',
        'source'=> 'NavigationElement');

    var $_observers = array( 'AMP_System_Permission_Observer_Tool');

    var $_allow_list = AMP_PERMISSION_CONTENT_NAVIGATION;
    var $_allow_save = AMP_PERMISSION_CONTENT_NAVIGATION;
    var $_allow_publish = AMP_PERMISSION_CONTENT_NAVIGATION;
    var $_allow_unpublish = AMP_PERMISSION_CONTENT_NAVIGATION;
    var $_allow_delete = AMP_PERMISSION_CONTENT_NAVIGATION;
    var $_allow_edit = AMP_PERMISSION_CONTENT_NAVIGATION;

    function onInitForm( &$controller ){

        //REQUEST values
        $tool_id = $controller->assert_var( 'tool_id' );
        if ( $tool_id ){
            $form = &$controller->get_form( );
            $form->setDefaultValue( 'modid', $tool_id );
        }
    }
}

?>
