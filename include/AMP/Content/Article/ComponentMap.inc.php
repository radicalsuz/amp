<?php

require_once ( 'AMP/System/ComponentMap.inc.php' );
require_once ( 'AMP/System/Page/Urls.inc.php');

class ComponentMap_Article extends AMPSystem_ComponentMap {

    var $heading = "Site Content";

    var $nav_name = "content";
    var $_action_default = 'list';
    var $_path_controller = 'AMP/Content/Article/Controller.php';
    var $_component_controller = 'Article_Component_Controller';
    var $_allow_search = true;
    var $url_system_default = AMP_SYSTEM_URL_ARTICLE;

    var $paths = array(
        'search_user' => 'AMP/Content/Article/Search/User/Form.inc.php',
        'search_fields_user' => 'AMP/Content/Article/Search/User/Fields.xml',
        'search' => 'AMP/Content/Article/Search/Form.inc.php',
        'search_fields' => 'AMP/Content/Article/Search/Fields.xml',
        'fields' => 'AMP/Content/Article/Fields.xml',
        'form'          => 'AMP/Content/Article/Form.inc.php',
        'source' => 'AMP/Content/Article.inc.php',
        'list' => 'AMP/Content/Article/ListForm.inc.php',
        'menu' => 'AMP/Content/Section/Menu.inc.php',
        'classlinks' => 'AMP/Content/Class/Links.inc.php' );

    var $components = array (
        'search_user' => 'ContentSearch_Form_User',
        'search' => 'ContentSearch_Form',
        'menu' => 'SectionMenu',
        'classlinks' => 'Class_Links',
        'list' => 'Article_ListForm',
        'form' => 'Article_Form',
        'source' => 'Article' );

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

    function onBeforeUpdate( &$controller ){
        $this->_save_version( $controller );
    }

    function _save_version( &$controller ){
        $model = &$controller->get_model( );
        $model_id = $controller->get_model_id( );
        if ( !isset( $model_id )) return;
        $model->readData( $model_id );
        if ( !$model->hasData( )) return;
        $model->saveVersion( );
    }

    function onBeforeDelete( &$controller ) {
        $this->_save_version( $controller );
    }
}
?>
