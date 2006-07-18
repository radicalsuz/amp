<?php
require_once( 'AMP/System/Component/Controller.php');

class PublicPage_Controller extends AMP_System_Component_Controller_Map {

    function PublicPage_Controller( ){
        $this->init( );
    }

    function commit_add( ){
        //just-in-time Build call is a performance optimization, sorry for the repetitive code
        $this->_init_form( );
        
        $this->_form->applyDefaults( );
        $this->_display->add( $this->_form, 'form' );
        return true;
    }

    function commit_save( ){
        //just-in-time Build call is a performance optimization, sorry for the repetitive code
        $this->_init_form( );
        
        //check if form validation succeeds
        if (!$this->_form->validate()) {
            $this->_display->add( $this->_form, 'form' );
            return false;
        }

        require_once( 'AMP/Content/Article.inc.php');
        $this->_model = &new Article( AMP_Registry::getDbcon( ));
        $this->_model->setData( $this->get_form_data( ));

        //attempt to save the submitted data
        if ( !$this->_model->save( )) {
            $this->error( $this->_model->getErrors( ));
            $this->_display->add( $this->_form );
            return false;
        }

        $this->_model_id = $this->_model->id;

        $this->message( sprintf( AMP_TEXT_DATA_SAVE_SUCCESS, $this->_model->getName( )));

        $this->_form->postSave( $this->_model->getData() );
        $this->redirect( AMP_Url_AddVars( AMP_SYSTEM_URL_CONTENTS, array( 'type='.$this->_model->getSection( ), 'AMPSearch=Search')));

    }

    function commit_cancel( ){
        $this->display_default( );
        return true;
    }


}

?>
