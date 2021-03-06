<?php

require_once( 'AMP/System/Form/XML.inc.php');
require_once( 'AMP/Content/Section/ComponentMap.inc.php');

class Section_Form extends AMPSystem_Form_XML {

    var $name_field = 'type';

    function Section_Form( ) {
        $name = 'articletype';
        $this->init( $name, 'POST', AMP_SYSTEM_URL_SECTION );
    }

    function setDynamicValues( ){
        $multiselect_boxes = array( 'list_by_class', 'list_by_section', 'list_by_tag');
        foreach( $multiselect_boxes as $field_name ) {
            if( $this->fields[ $field_name]['type'] == 'multiselect' ) {
                $this->addTranslation( $field_name, '_multiselectToText', 'get');
            }
        }
        $this->_send_preview_link_to_bottom( );
        $this->addTranslation( 'date2', '_makeDbDateTime', 'get' );
        $current_section_id = isset( $_GET['id']) ? intval( $_GET['id'] ) : false ;
        $this->fields['parent']['lookup']['var'] = $current_section_id;
        $this->fields['parent']['lookup']['name'] = 'section_map_excluding_section';
        if( !AMP_CONTENT_HUMANIZE_URLS) {
            unset( $this->fields['route_slug'] );
            unset( $this->fields['route_slug_info'] );
        }
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
    function _initJavascriptActions( ){
        $header = &AMP_get_header( );
        $this->_initPrettyUrlCreation( $header );
	}

    function _initPrettyUrlCreation( &$header ){
        if( !AMP_CONTENT_HUMANIZE_URLS ) return;
        $pretty_url_builder = <<<SCRIPT
                if( jq( 'form#articletype input[name=route_slug]' ).val( ) === "") {
                   jq( 'form#articletype textarea[name=title]').change(  function( ev ) {
                        var new_val =  jq( this ).val( ).replace( /[\s_]/g,'-').replace( /[^-A-z0-9]/g, '').toLowerCase( );
                        jq( 'form#articletype input[name=route_slug]' ).val( new_val );
                   });
                }
SCRIPT;
        $conflict_checker = <<<SCRIPT
               jq( 'form#articletype input[name=route_slug]').change( check_route_ajax );  
               jq( '#manual_route_check').click( check_route_ajax );  
			   function check_route_ajax( ev ) {
                    var system_domain = '%s';
					var target = jq( 'form#articletype input[name=route_slug]' );
                    jq.getJSON('/system/route_slug_ajax.php?slug_name=' + jq( target ).val() + '&ignore[0][owner_type]=%s&ignore[0][owner_id]=%s', function( result ) {
                        if ( result.conflicts !== undefined && result.conflicts.length == 0 ) {
                            jq( '#route_slug_details' ).html( "URL: " + system_domain + result.clean_url );
                        } else {
                            jq('#route_slug_details').html( "Warning: ");
                            jq.each( result.conflicts, function() {
                                jq('#route_slug_details').append( "This pretty url is already in use on <a href='" + this.owner_edit_url + "'>" + this.owner_type + " #"+ this.owner_id + "</a>" );
                            } );
                            jq('#route_slug_details').append( "<br/>Suggested Available URL: " + system_domain + result.clean_url );
                        }
                    } );
					return false;
               }
SCRIPT;
        $page_load_wrapper = <<<SCRIPT
            jq( function( ) {
                %s
            });
SCRIPT;
        $values = $this->getValues();
        $conflict_check = sprintf( $conflict_checker, AMP_SITE_URL, 'section',  $values['id'] );
        $header->addJavascriptDynamic( sprintf( $page_load_wrapper, $pretty_url_builder . $conflict_check ));
    }


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
