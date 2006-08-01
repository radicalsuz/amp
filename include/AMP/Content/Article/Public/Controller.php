<?php
require_once( 'AMP/System/Component/Controller.php');
require_once( 'AMP/Content/Page.inc.php');
require_once( 'AMP/Content/Manager.inc.php' );


class Article_Public_Component_Controller extends AMP_System_Component_Controller_Input {
    var $_display_class = 'AMPContent_Manager';
    var $_public_page_id = false;

    function Article_Public_Component_Controller( ){
        $this->init( );
    }

    function display_default( ){
        //do nothing
    }
/*
    function set_page( &$page ){
        $this->_page = &$page;
        $this->_display = &$page->_contentManager;
    }
    */

    function commit_add( ){
        $intro = &$this->_map->getPublicPage( 'input' );
        if ( !$intro ) return parent::commit_add( );

        $this->_public_page_id = $intro->id;
        $this->_display->add( $intro->getDisplay( ), AMP_CONTENT_DISPLAY_KEY_INTRO );

        $reg = &AMP_Registry::instance( );
        $reg->setEntry( AMP_REGISTRY_CONTENT_INTRO_ID, $this->_public_page_id );

        $this->_page->setIntroText( $this->_public_page_id );
        $this->_page->initLocation( );
        return parent::commit_add( );

    }

    function getPublicPageId( ){
        return $this->_public_page_id;
    }

    function commit_save( ){
        $this->_init_form( );

        //check if form validation succeeds
        if (!$this->_form->validate()) {
            $this->_display->add( $this->_form, 'form' );
            return false;
        }
        $this->_model->setDefaults( );
        $this->_model->setData( $this->get_form_data( ));

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

    function get_form_data( ){
        $results = parent::get_form_data( );
        if ( !empty( $this->_map->public_permitted_classes ) && isset( $results['class'])){
            if ( array_search( $results['class'], $this->_map->public_permitted_classes) !== FALSE ){
                $results['publish'] = 1;
            } else {
                $results['publish'] = 0;
                $results['class'] = AMP_CONTENT_CLASS_USERSUBMITTED;
            }
        }
        if ( !empty( $this->_map->public_permitted_sections ) && isset( $results['section'])){
            $results['publish'] =
               ( array_search( $results['section'], $this->_map->public_permitted_sections ) !== FALSE );
        }

        return $results;
    }

    function display_response( ){
        if ( $public_page = &$this->_map->getPublicPage( 'response' )) {
            $this->_public_page_id = $public_page->id;
            $this->_display->add( $public_page->getDisplay( ), AMP_CONTENT_DISPLAY_KEY_INTRO );

            $this->_page->setIntroText( $this->_public_page_id );
            $this->_page->initLocation( );
        }
    }

   function set_banner( $action, $heading = null ){
       //do nothing
   }
    
}

?>
