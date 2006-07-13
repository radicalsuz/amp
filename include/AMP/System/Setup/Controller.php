<?php

require_once( 'AMP/System/Component/Controller.php');
class AMP_System_Setup_Controller extends AMP_System_Component_Controller_Map {

    function AMP_System_Setup_Controller( ){
        $this->init( );
    }

    function _init_form_request( ){
        PARENT::_init_form_request( );
        $this->_model_id = AMP_SYSTEM_SETTING_DB_ID;
    }

    function commit_edit( ) {
        if ( !$this->_model->readData( $this->_model_id )) return $this->_commit_fail( );
        $this->_form->setValues( $this->_model->getData( ));
        $this->_display->add( $this->_form, 'form' );
        return true;
    }

    function commit_save( $copy_mode = false ){
        //check if form validation succeeds
        if (!$this->_form->validate()) {
            $this->_display->add( $this->_form, 'form' );
            return false;
        }
        $this->_model->setData( $this->get_form_data( $copy_mode ));

        //attempt to save the submitted data
        if ( !$this->_model->save( )) {
            $this->error( $this->_model->getErrors( ));
            $this->_display->add( $this->_form );
            return false;
        }

        $this->_model_id = $this->_model->id;

        $success_message = $copy_mode ? AMP_TEXT_DATA_COPY_SUCCESS : AMP_TEXT_DATA_SAVE_SUCCESS;
        $this->message( sprintf( $success_message, $this->_model->getName( )));

        $this->_form->postSave( $this->_model->getData() );
        $this->display_default( );
        return true;
    }

    function display_default( ){
       $display = &$this->_map->getComponent( 'form' );
       $this->_init_form( $display, false );
       $this->set_banner( 'edit');
       $this->_display->add( $display, 'default' );
       return true;
    }
}

?>
