<?php

require_once( 'AMP/System/Component/Controller.php');

class AMP_System_Setup_Wizard_Controller extends AMP_System_Component_Controller_Input {

    function AMP_System_Setup_Wizard_Controller( ){
        $this->init( );
    }

    function _init_form_request( &$form ){
        PARENT::_init_form_request( $form );
        $this->_model_id = AMP_SYSTEM_SETTING_DB_ID;
    }

    function commit_edit( ) {
        //just-in-time Build call is a performance optimization, sorry for the repetitive code
        $this->_init_form( );

        if ( !$this->_model->readData( $this->_model_id )) return $this->_commit_fail( );
        $this->_form->setValues( $this->_model->getData( ));
        $this->_display->add( $this->_form, 'form' );
        return true;
    }

    function commit_save( ){
        $result = PARENT::commit_save( );
        if ( !$result ) return false;
        $this->_saveSections( );
        ampredirect( AMP_SYSTEM_URL_HOME );
        return $result;
    }

    function _get_section_fieldname( $count, $type = 'name'){
        return 'section_' . $count . '_' . $type;
    }

    function _saveSections( ){
        require_once( 'AMP/Content/Section.inc.php' );
        $section_count = 1;
        $form = &$this->get_form( );
        while( isset(  $this->_request_vars[ $this->_get_section_fieldname( $section_count ) ]) 
                    && $this->_request_vars[ $this->_get_section_fieldname( $section_count ) ]){
            $name = $this->_request_vars[ $this->_get_section_fieldname( $section_count ) ];
            $text = $this->_request_vars[ $this->_get_section_fieldname( $section_count, 'text' ) ];

            $section = &new Section( AMP_Registry::getDbcon( ));
            $section->setName( $name );
            $section->setBlurb( $text );
            $section->setParent();
            $section->setListType();

            if ( !( $result = $section->save( ))){
                ++$section_count;
                continue;
            }

            $section->publish( );
            $section->reorder( $section_count );
            $form->setValues( array( 
                $this->_get_section_fieldname( $section_count ) => '',
                $this->_get_section_fieldname( $section_count, 'text') => ''
            ));
            $this->message( sprintf( AMP_TEXT_DATA_SAVE_SUCCESS, $section->getName( )));
            ++$section_count;
        }
    }

    function display_default( ){
       $display = &$this->_map->getComponent( 'form' );
       $this->_form = &$display;
       $this->_init_form( false );
       $this->set_banner( 'edit');
       $this->_display->add( $display, 'default' );
       if ( !$this->_form->isBuilt ) $this->_form->Build( );
       return true;
    }

}

?>
