<?php

require_once( 'AMP/System/Component/Controller.php' );

class Article_Component_Controller extends AMP_System_Component_Controller_Standard {

    function Article_Component_Controller( ){
        $this->init( );
    }


    function display_default( ) {
        $url = AMP_SYSTEM_URL_ARTICLE;
        if ( $url != $_SERVER['REQUEST_URI'] ) ampredirect( $url );
        return true;
        /*
        $display = &$this->_map->getComponent( 'list' );
        $display->setController( $this );

        $this->set_banner( 'list');
        $this->notify( 'initList' );

        $this->_display->add( $display, 'default' );
        return true;
        */
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

    function commit_restore( ){
        $version_id = $this->assert_var( 'vid' );
        if ( !$version_id ) return false;

        $this->notify( 'beforeUpdate' );
        $this->_model->readVersion( $version_id );
        if ( $result = $this->_model->save( ) ) {
            $this->message( sprintf( AMP_TEXT_DATA_RESTORE_SUCCESS, $this->_model->getName( )));
            ampredirect( AMP_Url_AddVars( AMP_SYSTEM_URL_ARTICLE, array( 'id='.$this->_model_id ) ));
            //$this->display_default( );
        }
        return $result;
    }

    function commit_delete_version( ){
        $version_id = $this->assert_var( 'vid' );
        if ( !$version_id ) return false;
        require_once( 'AMP/Content/Article/Version.inc.php');
        $version = & new Article_Version( AMP_Registry::getDbcon( ), $version_id );
        $this->_model_id = $version->getArticleId( );

        $name = $version->getName( );
        if ( !$name ) $name = AMP_TEXT_ITEM_NAME;
        if ( !$version->delete( )){
            if ( method_exists( $version, 'getErrors')) $this->error( $version->getErrors( ));
            ampredirect( AMP_Url_AddVars( AMP_SYSTEM_URL_ARTICLE_VERSION, array( 'id' => 'id='.$version_id )));
            return false;

        }
        $this->message( sprintf( AMP_TEXT_DATA_DELETE_VERSION_SUCCESS, $name, $version_id ));
        ampredirect( AMP_Url_AddVars( AMP_SYSTEM_URL_ARTICLE, array( 'id' => 'id='.$this->_model_id )));
        //$this->commit_edit( );
        return true;

    }

    function _commit_fails( ){
        ampredirect( AMP_SYSTEM_URL_HOME );
        return false;
    }


}

?>
