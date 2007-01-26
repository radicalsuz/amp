<?php

require_once ( 'AMP/Form/SearchForm.inc.php' );
require_once ( 'AMP/Content/Article/ComponentMap.inc.php');

class ContentSearch_Form extends AMPSearchForm {

    var $component_header = "Search Articles";

    function ContentSearch_Form (){
        $name = "AMP_ContentSearch";
        $this->init( $name, 'GET', AMP_SYSTEM_URL_ARTICLE );
    }

    function setDynamicValues() {
        $section_values = array( AMP_CONTENT_MAP_ROOT_SECTION => '-- ' . AMP_SITE_NAME . ' --');
		if($section_value_set = $this->_getValueSet( 'section' )) {
			$section_values += $section_value_set;
		}
        $this->setFieldValueSet( 'section', $section_values );
        //$this->_initJavascriptActions( );
        /*
        $map = &AMPContent_Map::instance();
        $this->setFieldValueSet( 'type',    $map->selectOptions() );
        $this->setFieldValueSet( 'class',   AMPContent_Lookup::instance('activeClasses'));
        $this->setFieldValueSet( 'publish',   AMPConstant_Lookup::instance('status'));
        */
    }

    function getJavascript( ){
        $this->_initJavascriptActions( );
    }

    function _initJavascriptActions( ){
        $header = &AMP_getHeader( );
        $header->addJavascriptOnload( 'new Ajax.Autocompleter( "title", "title_list", "ajax_request.php", {} );');
        $header->addJavascriptOnload( 'new Ajax.Autocompleter( "author", "author_list", "ajax_request.php", {} );');
        
    }


    function getComponentHeader() {
        return $this->component_header;
    }

    function _formFooter() {
        $renderer = &AMP_get_renderer( );
        $current_section_edit_link = false;
        $current_class_edit_link = false;
        $base_footer = '&nbsp;&nbsp;<a href="'. AMP_Url_AddVars( AMP_SYSTEM_URL_ARTICLE, array( 'nosearch=1' ) ) . '" class="standout">'
                . sprintf( AMP_TEXT_VIEW_ALL, AMP_pluralize( ucfirst( AMP_TEXT_ARTICLE ))) . '</a>';

        //sectional edit link
        $current_section = ( isset( $_REQUEST['section']) && $_REQUEST['section']) ? $_REQUEST['section'] : false;
        if ( !$current_section ) {
            $current_section = ( isset( $_REQUEST['type']) && $_REQUEST['type'] ? $_REQUEST['type'] : false );
        }
        if ( $current_section ) {
            $section_names = AMPContent_Lookup::instance( 'sections');
            $section_name = isset( $section_names[$current_section]) ? $section_names[$current_section] : false;
            $current_section_edit_link = 
                    $renderer->separator( )
                    . $renderer->link( 
                        AMP_Url_AddVars( AMP_SYSTEM_URL_SECTION, array( 'id='.$current_section )),
                            $renderer->image( AMP_SYSTEM_ICON_EDIT, array( 'width' => '16', 'height' => '16', 'border' => 0 ) )
                            . $renderer->space( ) 
                            . AMP_TEXT_EDIT 
                            . $renderer->space( ) 
                            . AMP_TEXT_SECTION 
                            . $renderer->space( ) 
                            . AMP_trimText( $section_name, 20, false )
                        );
        }

        //class edit link
        $current_class = ( isset( $_REQUEST['class']) && $_REQUEST['class']) ? $_REQUEST['class'] : false;
        if ( $current_class ) {
            $class_names = AMPContent_Lookup::instance( 'classes' );
            $class_name = ( isset( $class_names[$current_class ])) ? $class_names[ $current_class ] : false;
            $current_class_edit_link = 
                    $renderer->separator( )
                    .$renderer->link( 
                        AMP_Url_AddVars( AMP_SYSTEM_URL_CLASS, array( 'id='.$current_class )),
                            $renderer->image( AMP_SYSTEM_ICON_EDIT , array( 'width' => '16', 'height' => '16', 'border' => 0 ) )
                            . $renderer->space( ) 
                            . AMP_TEXT_EDIT 
                            . $renderer->space( ) 
                            . AMP_TEXT_CLASS. $renderer->space( ) 
                            . AMP_trimText( $class_name, 20, false )
                            );
                    
        }
        return $base_footer 
                .$current_section_edit_link
                .$current_class_edit_link
                . $renderer->newline( );
    }

    function getSearchValues( ) {
        $results = parent::getSearchValues( );
        if ( !(isset( $results['search_by_date']) && $results['search_by_date'])) unset ( $results['date'] );
        unset( $results['search_by_date']);
        $results['allowed'] = 1;
        return $results;
    }

    function submitted() {
        $search_request = (  ( isset( $_REQUEST['type'] ) && $_REQUEST['type']  )
                          || ( isset( $_REQUEST['class'] ) && $_REQUEST[ 'class' ])
                          || ( isset( $_REQUEST['section'] ) && $_REQUEST[ 'section' ])
                          );
        if ( isset( $_REQUEST['action']) && array_search( $_REQUEST['action'] , array( AMP_TEXT_LIST, AMP_TEXT_SEARCH )) == FALSE ){
            $search_request = false;
        }
        if ( $search_request ) return 'search';
        return parent::submitted( );
    }

}
?>
