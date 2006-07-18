<?php

require_once( 'AMP/System/Component/Controller.php');

class AMP_System_Component_Controller_Public extends AMP_System_Component_Controller_Input {
    var $_display_class = 'AMPContent_Manager';
    var $_public_page_id = false;

    function AMP_System_Component_Controller_Public ( ){
        $this->init( );
    }

    function commit_add( ){
        $intro = &$this->_map->getPublicPage( 'input' );
        if ( !$intro ) return PARENT::commit_add( );

        $this->_public_page_id = $intro->id;
        $this->_display->add( $intro->getDisplay( ));

        $reg = &AMP_Registry::instance( );
        $reg->setEntry( AMP_REGISTRY_CONTENT_INTRO_ID, $this->_public_page_id );

        $this->_page->setIntroText( $this->_public_page_id );
        $this->_page->initLocation( );
        return PARENT::commit_add( );

    }

    function commit_save( ){
        if ( !$this->_form->isBuilt ) $this->_form->Build( );

        //check if form validation succeeds
        if (!$this->_form->validate()) {
            $this->_display->add( $this->_form, 'form' );
            return false;
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
}

?>
