<?php

require_once( 'AMP/System/Form/XML.inc.php');
require_once( 'AMP/Content/Section/ComponentMap.inc.php');

class Section_Form extends AMPSystem_Form_XML {

    var $name_field = 'type';

    function Section_Form( ) {
        $name = 'articletype';
        $this->init( $name, 'POST', AMP_SYSTEM_URL_SECTION );
    }

    function _after_init( ) {
        $this->addTranslation( 'list_by_class', 'to_text', 'get');
        $this->addTranslation( 'list_by_section', 'to_text', 'get');
        $this->addTranslation( 'list_by_tag', 'to_text', 'get');
        $this->_send_preview_link_to_bottom( );
    }

    function to_text( $data, $fieldname ) {
        if ( !( isset( $data[$fieldname]) && $data[$fieldname])) return false;
        return join( ", ", $data[$fieldname]);
    }

    function _init_submit( ) {
        $this->defineSubmit( 
                'delete',
                'Delete Record',
                array ( 
                    'onclick' => 
                    "return confirmSubmit('".AMP_TEXT_RECORD_CONFIRM_DELETE_SECTION . AMP_TEXT_RECORD_CONFIRM_DELETE."');" )
                );
        if ($this->allow_copy) $this->copy_button();
    }
    function setDynamicValues( ){

        $this->addTranslation( 'date2', '_makeDbDateTime', 'get' );
    }

    /*
    function adjustFields( $fields ) {
        $id_display = $this->_setIdDisplay( );
        if ( $id_display ) $fields['id_display'] = $id_display;
        return $fields;
    }
    */

    function _send_preview_link_to_bottom( ) {
        //lower preview link
        $page = AMP_System_Page_Display::instance( );
        $page->setDisplayOrder( array( 
            AMP_CONTENT_DISPLAY_KEY_FLASH,
            AMP_CONTENT_DISPLAY_KEY_INTRO,
            AMP_CONTENT_DISPLAY_KEY_BUFFER,
            'form',
            'preview_link'
            ));


    }

    function _formHeader( ){
        $id = $this->getIdValue( );
        if ( !$id ) return false;

        require_once( 'AMP/Content/Section.inc.php');
        require_once( 'AMP/Content/Section/Display/Info.php');

        $section = &new Section( AMP_Registry::getDbcon( ), $id ) ;
        $display = &new AMP_Content_Section_Display_Info( $section );
        return $display->execute( );

    }

    function _setIdDisplay( ){
        if ( !( $id = $this->getIdValue( ))) return false;
        require_once( 'AMP/Content/Display/HTML.inc.php');
        $renderer = &new AMPDisplay_HTML;
        $value = $renderer->in_P( 'ID: ' . $id, array( 'class' => 'name'));
       
        return
             array( 
                'type' => 'static',
                'default' =>  $value,
                ); 
    }

    function _selectAddNull( $valueset, $name ) {
        if ( $name != 'parent' ) return parent::_selectAddNull( $valueset, $name );
        return array( AMP_CONTENT_MAP_ROOT_SECTION => '-- ' . AMP_CONTENT_SECTION_NAME_ROOT . ' --') + $valueset;
    }

    function _blankValueSet( $valueset, $name ){
        if ( $name != 'parent' ) return parent::_blankValueSet( $valueset, $name );
        return array( AMP_CONTENT_MAP_ROOT_SECTION => '-- ' .AMP_CONTENT_SECTION_NAME_ROOT . ' --');

    }

    function _formFooter( ){
        if ( !$this->getIdValue( )) return false;
        $renderer = &new AMPDisplay_HTML;
        return $renderer->inSpan( AMP_TEXT_SYSTEM_LINK_NAV_LAYOUT_EDIT, array( 'class' => 'intitle'))  
                . AMP_navCountDisplay_Section( $this->getIdValue( ) );
    }

    function validate( ){
        $section_id = isset( $_REQUEST['parent']) && $_REQUEST['parent'] ? $_REQUEST['parent'] : false;
        if ( $section_id && !AMP_allow( 'access', 'section', $section_id )) {
            $flash = AMP_System_Flash::instance( );
            $flash->add_error( sprintf( AMP_TEXT_ERROR_ACTION_NOT_ALLOWED, AMP_TEXT_SAVE ));
            return false;
        }
        return parent::validate( );
    }

}
?>
