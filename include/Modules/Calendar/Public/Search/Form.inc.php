<?php

require_once ( 'AMP/Form/SearchForm.inc.php' );
require_once ( 'Modules/Calendar/ComponentMap.inc.php' );

class Calendar_Public_Search_Form extends AMPSearchForm {

    var $component_header = "Search Calendar";

    function Calendar_Public_Search_Form ( ) {
        $name = "Calendar_Search";
        $this->init( $name, 'GET', AMP_CONTENT_URL_EVENT );
    }

    function adjustFields( $fields ) {
        if ( !isset( $_GLOBALS['nonstateregion']) && !( defined( 'AMP_CALENDAR_USE_REGIONS'))) {
            unset( $fields['region']);
        } else {
            unset( $fields['state']);
        }
        return $fields;
    }

    function submitted( ) {
        if ( isset( $_GET['old']) && $_GET['old']) return true;
        return parent::submitted( );
    }

}

?>
