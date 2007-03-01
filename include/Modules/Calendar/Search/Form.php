<?php

require_once ( 'AMP/Form/SearchForm.inc.php' );
require_once ( 'Modules/Calendar/ComponentMap.inc.php');

class Calendar_Search_Form extends AMPSearchForm {

    var $component_header = "Search Calendar";

    function Calendar_Search_Form ( ) {
        $name = "Calendar_Search";
        $this->init( $name, 'GET', AMP_SYSTEM_URL_EVENT );
    }

    function _initJavascriptActions( ) {
        $header = AMP_get_header( );
        $header->addJavascriptOnload( 'new Ajax.Autocompleter( "event", "event_list", "ajax_request.php", {} );');
    }

    function adjustFields( $fields ) {
        if ( !isset( $_GLOBALS['nonstateregion']) && !( defined( 'AMP_CALENDAR_USE_REGIONS'))) {
            unset( $fields['region']);
        } else {
            unset( $fields['state']);
        }
        return $fields;
    }
}

?>
