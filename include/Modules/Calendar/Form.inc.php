<?php

require_once( 'AMP/System/Form/XML.inc.php');
require_once( 'Modules/Calendar/ComponentMap.inc.php');
require_once( 'Modules/Calendar/Lookups.inc.php');

class Calendar_Form extends AMPSystem_Form_XML {

    var $name_field = 'event';

    function Calendar_Form( ) {
        $name = 'Calendar';
        $this->init( $name, 'POST', AMP_SYSTEM_URL_EVENT );
    }

    function setDynamicValues( ){
       $this->addTranslation( 'date', '_makeDbDateTime', 'get');
       $this->addTranslation( 'lstate', '_makeStateAbbrev', 'set');
    }

    function adjustFields( $fields ) {
        if ( !isset( $_GLOBALS['nonstateregion']) && !( defined( 'AMP_CALENDAR_USE_REGIONS'))) {
            unset( $fields['region']);
        }
        if ( defined( 'AMP_CALENDAR_ALLOW_RSVP' ) && AMP_CALENDAR_ALLOW_RSVP ) {
            $rsvp_fields = $this->_readXML( 'Modules/Calendar/RsvpFields.xml' );
            if ( $rsvp_fields ) {
                $fields = AMP_array_splice( $fields, 'desc_header', null, $rsvp_fields );
            }
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
