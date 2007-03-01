<?php

require_once( 'AMP/System/Form/XML.inc.php');
require_once( 'Modules/Calendar/ComponentMap.inc.php');
require_once( 'Modules/Calendar/Lookups.inc.php');

class Calendar_Public_Form extends AMPSystem_Form_XML {

    var $name_field = 'event';

    function Calendar_Public_Form( ) {
        $name = 'Calendar';
        $this->init( $name, 'POST', AMP_CONTENT_URL_EVENT_ADD );
    }

    function setDynamicValues( ){
       $this->addTranslation( 'date', '_makeDbDateTime', 'get');
       $this->addTranslation( 'lstate', '_makeStateAbbrev', 'set');
    }

    function adjustFields( $fields ) {
        if ( !isset( $_GLOBALS['nonstateregion']) && !( defined( 'AMP_CALENDAR_USE_REGIONS'))) {
            unset( $fields['region']);
        }
        return $fields;
    }

    function _makeStateAbbrev( $data, $fieldname ) {
        if ( !isset( $data[$fieldname])) return false;
        if ( !is_numeric( $data[$fieldname])) return $data[ $fieldname ];

        $states = AMP_lookup( 'states');
        if ( isset( $states[$data[$fieldname]])) {
            return $states[$data[$fieldname]];
        }
        return $data[$fieldname];
    }
}

?>
