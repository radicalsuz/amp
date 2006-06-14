<?php

require_once( 'AMP/System/Component/Controller.php' );

class Article_Component_Controller extends AMP_System_Component_Controller_Standard {

    function Article_Component_Controller( ){
        $this->init( );
    }

    function commit_default( ){
        // override
    }

    function display_default( ) {
        $display = &$this->_map->getComponent( 'list' );
        $display->setController( $this );
        //$searchform = &$this->_map->getComponent( 'search' );
        //$searchform->Build( );

        $this->set_banner( 'list');
        $this->notify( 'initList' );

        //$this->_display->add( $searchform, 'search' );
        $this->_display->add( $display, 'default' );
        return true;
    }

    function commit_view( ){
        $search = &$this->_map->getComponent( 'search', 'AMP_Content_Search' );
        $this->add_component_header( AMP_TEXT_SEARCH, AMP_Pluralize( $this->_map->getHeading( )));
        $this->_display->add( $search, 'search' );

        $search->Build( true );
        //if ( !$search->submitted( ))
        $search->applyDefaults( );

        $menu_display  = &$this->_map->getComponent( 'menu' );
        $class_display = &$this->_map->getComponent( 'classlinks' );
        $this->add_component_header( AMP_TEXT_CONTENT_MAP_HEADING , "");
        $this->_display->add( $menu_display, 'menu' );
        $this->add_component_header( AMP_TEXT_VIEW, AMP_TEXT_BY . " " . ucfirst( AMP_TEXT_CLASS ) );
        $this->_display->add( $class_display, 'class' );
        
    }


}

?>
