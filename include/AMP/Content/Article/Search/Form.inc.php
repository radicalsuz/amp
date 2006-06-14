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
        $section_values = $this->_getValueSet( 'section' );
        $section_values = array( AMP_CONTENT_MAP_ROOT_SECTION => '-- ' . AMP_SITE_NAME . ' --') + $section_values ;
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
        return '&nbsp;&nbsp;<a href="'. AMP_SYSTEM_URL_ARTICLE . '" class="standout">'
                . sprintf( AMP_TEXT_VIEW_ALL, AMP_Pluralize( ucfirst( AMP_TEXT_ARTICLE ))) . '</a><BR />';
    }

    function getSearchValues( ) {
        $results = &PARENT::getSearchValues( );
        if ( !(isset( $results['search_by_date']) && $results['search_by_date'])) unset ( $results['date'] );
        unset( $results['search_by_date']);
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
        return PARENT::submitted( );
    }

}
?>
