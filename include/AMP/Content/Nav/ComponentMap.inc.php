<?php

require_once( 'AMP/System/ComponentMap.inc.php');

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
