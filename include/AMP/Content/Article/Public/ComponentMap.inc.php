<?php

require_once ( 'AMP/System/ComponentMap.inc.php' );
require_once ( 'AMP/BaseDB.php' );
require_once ( 'AMP/Content/Config.inc.php' );

class ComponentMap_Article_Public extends AMPSystem_ComponentMap {

    var $heading = "Site Content";

    var $nav_name = "content";
    var $_action_default = 'add';
    var $_path_controller = 'AMP/Content/Article/Public/Controller.php';
    var $_component_controller = 'Article_Public_Component_Controller';
    var $_public_page_id_input = AMP_CONTENT_PUBLICPAGE_ID_ARTICLE_INPUT;
    var $_public_page_id_response = AMP_CONTENT_PUBLICPAGE_ID_ARTICLE_RESPONSE;
    var $public_permitted_sections = array( );
    var $public_permitted_classes  = array( );

    var $paths = array(
        'fields' => 'AMP/Content/Article/Public/Fields.xml',
        'form'          => 'AMP/Content/Article/Public/Form.inc.php',
        'source' => 'AMP/Content/Article.inc.php',
        );

    var $components = array (
        'form' => 'Article_Public_Form',
        'source' => 'Article' 
        );

    function onInitForm( &$controller ){

        $form = &$controller->get_form( );
        if ( $section_def = $form->getField( 'section' ) && !empty( $this->public_permitted_sections )){
            $section_values = &AMPContent_Lookup::instance( 'sectionMap' );
            $allowed_sections = array_combine_key( $this->public_permitted_sections, $section_values );
            $allowed_ordered_sections = array_combine_key( $section_values, $allowed_sections );
            $form->setFieldValueSet( 'section', $allowed_ordered_sections );
        }
        if ( $class_def = $form->getField( 'class' ) && !empty( $this->public_permitted_classes )){
            $class_values = &AMPContent_Lookup::instance( 'classes');
            $allowed_classes = array_combine_key( $this->public_permitted_classes, $class_values );
            $form->setFieldValueSet( 'class', $allowed_classes );
        }
    }

}
?>
