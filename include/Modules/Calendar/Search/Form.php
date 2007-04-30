<?php

require_once ( 'AMP/Form/SearchForm.inc.php' );
require_once ( 'Modules/Calendar/ComponentMap.inc.php');
require_once ( 'Modules/Calendar/Lookups.inc.php');

class Calendar_Search_Form extends AMPSearchForm {

    var $component_header = "Search Calendar";

    function Calendar_Search_Form ( ) {
        $name = "Calendar_Search";
        $this->init( $name, 'GET', $_SERVER['PHP_SELF']);
    }

    function _initJavascriptActions( ) {
        $header = AMP_get_header( );
        $header->addJavascriptOnload( 'new Ajax.Autocompleter( "event", "event_list", "ajax_request.php", {} );');
    }

    function adjustFields( $fields ) {
        $owned_events = AMP_lookup( 'formsWithEvents');
        if ( !$owned_events ) {
            unset( $fields['modin'] );
            $fields['modin']['type'] = 'hidden';
        }

        //region adjustment
        if ( !isset( $_GLOBALS['nonstateregion']) && !( defined( 'AMP_CALENDAR_USE_REGIONS'))) {
            unset( $fields['region']);
        } else {
            unset( $fields['state']);
        }
        return $fields;
    }
}

?>
