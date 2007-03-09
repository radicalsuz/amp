<?php

require_once( 'AMP/System/Component/Controller.php');

class AMP_System_Component_Controller_Public extends AMP_System_Component_Controller_Input {
    var $_display_class = 'AMPContent_Manager';
    var $_public_page_id = false;
    var $_action_detail = 'view';

    function AMP_System_Component_Controller_Public ( ){
        $this->init( );
    }

    function commit_add( ){
        $intro = &$this->_map->getPublicPage( 'input' );
        if ( !$intro ) return parent::commit_add( );

        $this->_public_page_id = $intro->id;
        $this->_display->add( $intro->getDisplay( ));

        $reg = &AMP_Registry::instance( );
        $reg->setEntry( AMP_REGISTRY_CONTENT_INTRO_ID, $this->_public_page_id );

        $this->_page->setIntroText( $this->_public_page_id );
        $this->_page->initLocation( );
        return parent::commit_add( );

    }

    function commit_save( ) {
        if ( !$this->_form->isBuilt ) $this->_form->Build( );

        //check if form validation succeeds
        if (!$this->_form->validate()) {
            $flash = AMP_System_Flash::instance( );
            $flash->add_error( AMP_TEXT_ERROR_FORM_DATA_INVALID );

            $intro = &$this->_map->getPublicPage( 'input' );
            $this->_set_public_page( $intro );
            $this->_display->add( $this->_form, 'form' );
            return true;
        }

        $this->notify( 'beforeUpdate' );
        if ( !isset( $this->_model->id )) $this->_model->setDefaults( );

        $this->_model->mergeData( $this->get_form_data( ));

        $this->notify( 'beforeSave' );
        //attempt to save the submitted data
        if ( !$this->_model->save( )) {
            $this->error( $this->_model->getErrors( ));
            $this->_display->add( $this->_form );
            return false;
        }

        $this->_model_id = $this->_model->id;

        /*
        $success_message = AMP_TEXT_DATA_SAVE_SUCCESS;
        $this->message( sprintf( $success_message, $this->_model->getName( )));
        */

        $this->_form->postSave( $this->_model->getData() );
        $this->display_response( );
        return true;
    }

    function getPublicPageId( ){
        return $this->_public_page_id;
    }

    function display_response( ){
        if ( $public_page = &$this->_map->getPublicPage( 'response' )) {
            $this->_public_page_id = $public_page->id;
            $this->_display->add( $public_page->getDisplay( ));
        }
    }

    function display_default( ){
        //do nothing
    }

    function set_banner( $action, $heading = null ){
        //do nothing
    }

    function commit_view( ) {
        $intro = &$this->_map->getPublicPage( 'detail' );
        $this->_set_public_page( $intro );

        if ( !isset( $this->_model_id) && $this->_model_id ) {
            return false;
        }
        if ( !$this->_model->readData( $this->_model_id )) return $this->_commit_fail( );
        $this->display_search( );

        $display = $this->_map->getComponent( 'view', $this->_model );
        $this->_display->add( $display, 'view');
        return true;
    }

    function display_search( ) {
        $search = $this->_map->getComponent( 'search' );
        if ( !$search ) return;
        $search->Build( true );
        $search->applyDefaults( );
        $this->_display->add( $search, 'search');

    }

    function _set_public_page( &$public_page ) {
        if ( !$public_page ) return;

        $this->_public_page_id = $public_page->id;
        $this->_display->add( $public_page->getDisplay( ));

        $reg = &AMP_Registry::instance( );
        $reg->setEntry( AMP_REGISTRY_CONTENT_INTRO_ID, $this->_public_page_id );

        $this->_page->setIntroText( $this->_public_page_id );
        $this->_page->initLocation( );
    }

    function commit_cancel( ){
        ampredirect( AMP_CONTENT_URL_INDEX );
        return true;
    }

}

?>
