<?php

require_once( 'AMP/System/Component/Controller.php' );

class Article_Component_Controller extends AMP_System_Component_Controller_Standard {
    // caching the add form messes up a bunch of javascript stuff
    // var $_actions_cacheable = array( 'add', 'new' );

    function Article_Component_Controller( ){
        $this->init( );
    }


    function display_default( ) {
        if ( defined( 'AMP_CONTENT_PAGE_REDIRECT' )) return true;
        $url = AMP_SYSTEM_URL_ARTICLE;

        //check for saved cookie
        $display_class = strtolower( $this->_map->components['list'] );
        $list_location_cookie =  $display_class  . '_ListLocation';
        if ( isset( $_COOKIE[ $list_location_cookie ]) && $_COOKIE[ $list_location_cookie ]) {
            $url = $_COOKIE[ $list_location_cookie ];
        }

        $current_url = $_SERVER['REQUEST_URI'];
        if ( $current_url != $url ) {
            ampredirect( $url );
        }
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
        $this->add_component_header( AMP_TEXT_SEARCH, AMP_pluralize( $this->_map->getHeading( )));
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
        $this->_clear_list_location( );
        
    }

    function _clear_list_location( ){
        $display_class = strtolower( $this->_map->components['list'] );
        $list_location_cookie = $display_class  . '_ListLocation';
        setcookie( $list_location_cookie, "");
    }

    function commit_restore( ){
        $version_id = $this->assert_var( 'vid' );
        if ( !$version_id ) return false;

        $this->notify( 'beforeUpdate' );
        $this->_model->readVersion( $version_id );
        if ( $result = $this->_model->save( ) ) {
            $this->notify( 'restore' );
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

    function _commit_default( ){
        //view never wants the standard article list
        //save might show the list if the form fails to validate
        $no_list_actions = array( 'view', 'save');
        if ( array_search( $this->get_action( ), $no_list_actions ) !== FALSE ) {
            return;
        }
        return parent::_commit_default( );
    }

    function _commit_fail( ){
        ampredirect( AMP_SYSTEM_URL_HOME );
        return false;
    }

    function commit_list( ){
        $display_class = strtolower( $this->_map->components['list'] );
        $list_location_cookie = $display_class  . '_ListLocation';

        if ( $this->_is_basic_list_request( ) ) {
            if ( isset( $_COOKIE[ $list_location_cookie ]) && $_COOKIE[ $list_location_cookie]
                 && ( $_COOKIE[ $list_location_cookie ] != $_SERVER['REQUEST_URI'] )) {
                ampredirect( $_COOKIE[ $list_location_cookie ]);
            }
        } else {
            setcookie( $list_location_cookie, $_SERVER['REQUEST_URI']);
        }
        return parent::commit_list( );
        
    }

    function _is_basic_list_request( ){
        if ( !empty( $_POST )) return false;
        if ( strpos( $_SERVER['REQUEST_URI'], AMP_SYSTEM_URL_ARTICLE ) === FALSE ) return true;
        $request_vars = AMP_URL_Read( );
        if ( empty( $request_vars )) return true;

        $action_value = ( isset( $request_vars['action'] ) && $request_vars['action']) ? $request_vars['action'] : false;
        $action_value = $this->assert_var( 'action'); 
        if ( $action_value && $action_value != 'list' ) return false;
        if ( count( $request_vars ) > 1  || !$action_value ) return false;
        return true;
    }

    function update_list_location( $item_id, $location_item = 'section' ) {
        $display_class = strtolower( $this->_map->components['list'] );
        $list_location_cookie = $display_class  . '_ListLocation';
        $list_location_var = $location_item . '=' . $item_id;

        if ( !( isset( $_COOKIE[ $list_location_cookie ]) && $_COOKIE[ $list_location_cookie] )) {
            return false;
        }
        //confirm that existing cookie is for a similar search
        if ( strpos( $_COOKIE[ $list_location_cookie ], $location_item ) 
            && !strpos( $_COOKIE[ $list_location_cookie ], $location_item.'&' )) {

            $new_url = AMP_Url_AddVars( AMP_SYSTEM_URL_ARTICLE, $list_location_var );
            ampredirect( $new_url );
        }
    }

    function show_preview_link( ) {
        $this->_display->setDisplayOrder( array( 
            AMP_CONTENT_DISPLAY_KEY_FLASH,
            AMP_CONTENT_DISPLAY_KEY_INTRO,
            AMP_CONTENT_DISPLAY_KEY_BUFFER,
            'form',
            'preview_link'
        ));
        parent::show_preview_link( );
    }

}

?>
