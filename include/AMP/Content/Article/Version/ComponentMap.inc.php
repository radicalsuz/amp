<?php

require_once ( 'AMP/System/ComponentMap.inc.php' );

class ComponentMap_Article_Version extends AMPSystem_ComponentMap {

    var $heading = "Archived Content";

    var $nav_name = "content";
    var $_action_default = 'list';
    var $_path_controller = 'AMP/Content/Article/Controller.php';
    var $_component_controller = 'Article_Component_Controller';
    var $_allow_search = true;
    var $url_system_default = AMP_SYSTEM_URL_ARTICLE;

    var $paths = array(
        'fields' => 'AMP/Content/Article/Fields.xml',
        'form'   => 'AMP/Content/Article/Version/Form.inc.php',
        'source' => 'AMP/Content/Article/Version.inc.php',
        'list'   => 'AMP/Content/Article/Version/List.inc.php',
        );

    var $components = array (
        'list'   => 'Article_Version_List',
        'form'   => 'Article_Version_Form',
        'source' => 'Article_Version' 
        );

/*
    function ComponentMap_Article_Version( ){
        $this->__construct( );
    }
    function __construct( ){

    }
    function onInitForm( &$controller ){

        $class_id = $controller->assert_var( 'class' );
        $section_id = $controller->assert_var( 'section' );
        if ( !( $class_id || $section_id )) return false;

        $form = &$controller->get_form( );
        if ( $section_id ){
            $form->setDefaultValue( 'section', $section_id );
        }
        if ( $class_id ){
            $form->setDefaultValue( 'class', $class_id );
        }
    }
    */
}
?>
