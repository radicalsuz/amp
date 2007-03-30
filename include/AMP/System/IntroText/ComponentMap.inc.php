<?php

require_once ( 'AMP/System/ComponentMap.inc.php' );
require_once( 'AMP/System/Permission/Observer/Section.php' );
require_once( 'AMP/System/Permission/Observer/Tool.php' );

class ComponentMap_IntroText extends AMPSystem_ComponentMap {

    var $heading = "Public Page";
    var $nav_name = "tools";
    var $_allow_inline_update = true;
    var $_action_default = 'list';

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

    var $_observers = array( 'AMP_System_Permission_Observer_Section', 'AMP_System_Permission_Observer_Tool' );

    var $_allow_list = AMP_PERMISSION_TOOLS_INTROTEXT ;
    var $_allow_edit = AMP_PERMISSION_TOOLS_INTROTEXT ;
    var $_allow_save = AMP_PERMISSION_TOOLS_INTROTEXT;
    var $_allow_publish = AMP_PERMISSION_TOOLS_INTROTEXT;
    var $_allow_unpublish = AMP_PERMISSION_TOOLS_INTROTEXT;
    var $_allow_delete = AMP_PERMISSION_TOOLS_INTROTEXT;

    function onInitForm( &$controller ){
        $tool_id = $controller->assert_var( 'tool_id' );
        $form_id = $controller->assert_var( 'form_id' );
        if (!( $tool_id || $form_id )) return false;
        $form = &$controller->get_form( );
        if ( $tool_id ) {
            $form->setDefaultValue( 'modid', $tool_id );
        }
        if ( $form_id ) {
            $form->setValues( array( 'list_form_id'=> $form_id ));
        }
    }

    function onSave( &$controller ) {
        $form = $controller->get_form( );
        $values = $form->getValues( );
        if ( isset( $values['list_form_id']) && $values['list_form_id']) {
            ampredirect( AMP_url_add_vars( AMP_SYSTEM_URL_TOOL_PUBLICPAGE, array( 'modid=' . $values['modid']) ));
        }
    }

    /**
     * &getComponent 
     *  
     * this is an ugly workaround that seems to fix a compatibility issue with PHP4
     *
     * @param mixed $component_type 
     * @param mixed $passthru 
     * @access public
     * @return void
     */
    function &getComponent( $component_type, $passthru = null ) {
        return parent::getComponent( $component_type, $passthru );
    }

}
?>
