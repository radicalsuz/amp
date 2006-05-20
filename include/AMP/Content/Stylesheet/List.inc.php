<?php
require_once( 'AMP/System/List/Form.inc.php');
require_once( 'AMP/Content/Stylesheet/Stylesheet.php');

class AMP_Content_Stylesheet_List extends AMP_System_List_Form {
    var $col_headers = array( 
        'File Name' => 'name',
        'Last Edited' => 'time',
        'Locations' => '_showSheetLocations');

    var $_source_object = 'AMP_Content_Stylesheet';
    var $editlink = AMP_SYSTEM_URL_STYLESHEET;
    var $_observers_source = array( 'AMP_System_List_Observer' );
    var $_actions = array( 'delete' );
    var $_url_add = 'stylesheet.php?action=new';

    function AMP_Content_Stylesheet_List( ) {
        $listSource = &new $this->_source_object( );
        $source = & $listSource->search( );
        $this->init( $source );
        $this->addTranslation( 'time', '_makePrettyDateTime');
    }

    function _getSourceRow( ) {
        $row_data = PARENT::_getSourceRow( );
        if ( $row_data ) $row_data['id'] = $row_data['name'];
        return $row_data;
    }

    function _showSheetLocations( &$source, $fieldname ){
        $filename = $source->getName( );
        $section_locations = AMPContentLookup_StylesheetLocationSections::instance( $filename );
        $template_locations = AMPContentLookup_StylesheetLocationTemplates::instance( $filename );
        if ( !( $section_locations || $template_locations)) return false; 

        $section_names = AMPContent_Lookup::instance( 'sections');
        $template_names = AMPSystem_Lookup::instance( 'templates');

        $output = '';
        if ( $section_locations ) {
            $output .= $this->_renderSheetLocations( $section_locations, $section_names, AMP_TEXT_SECTION, AMP_SYSTEM_URL_SECTION );
        }
        if ( $template_locations ){
            $output .= $this->_renderSheetLocations( $template_locations, $template_names, AMP_TEXT_TEMPLATE, AMP_SYSTEM_URL_TEMPLATE );
        }
        return $output;
        
    }

    function _renderSheetLocations( $locations, $names, $text_description, $system_edit_link ) {
        $output = '';
        $renderer = &$this->_getRenderer( );
        foreach( $locations as $location_id => $stylesheet_setting ){
            $output .=  ucfirst( $text_description ). ': ' 
                        . $renderer->link( AMP_URL_AddVars( $system_edit_link, array( 'action=edit','id=' . $location_id )), 
                                        $names[ $location_id ])
                        . $renderer->newline( );
    }
        return $renderer->inDiv( $output, array( 'style' => 'padding:2px;'));

    }

}
?>
