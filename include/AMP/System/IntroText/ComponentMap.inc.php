<?php

require_once ( 'AMP/System/ComponentMap.inc.php' );

class ComponentMap_IntroText extends AMPSystem_ComponentMap {

    var $heading = "Public Page";
    var $nav_name = "tools";
    var $_allow_inline_update = true;

    var $paths = array(
        'fields' => 'AMP/System/IntroText/Fields.xml',
        'form' => 'AMP/System/IntroText/Form.inc.php',
        'list' => 'AMP/System/IntroText/List.inc.php',
        'copier' => 'AMP/System/IntroText/Copy.inc.php',
        'source' => 'AMP/System/IntroText.inc.php' );

    var $components = array (
        'form' => 'AMPSystem_IntroText_Form',
        'list' => 'AMPSystem_IntroText_List',
        'copier' => 'AMPSystem_IntroText_Copy',
        'source' => 'AMPSystem_IntroText' );

    function onInitForm( &$controller ){
        if (!( $tool_id = $controller->assert_var( 'tool_id' ))) return false;
        $form = &$controller->get_form( );
        $form->setDefaultValue( 'modid', $tool_id );
    }

}
?>
