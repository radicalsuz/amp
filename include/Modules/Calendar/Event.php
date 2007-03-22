<?php

require_once( 'AMP/System/Data/Item.inc.php');

class Calendar_Event extends AMPSystem_Data_Item {

    var $datatable = "calendar";
    var $name_field = "event";
    var $_class_name = 'Calendar_Event';
    var $_exact_value_fields = array( 'id', 'typeid', 'region' );
    var $_sort_auto = false;

    function Calendar_Event ( &$dbcon, $id = null ) {
        $this->init( $dbcon, $id );
    }

    function _adjustSetData( $data ) {
        $this->legacyFieldname( $data, 'shortdesc', 'blurb' );
        $this->legacyFieldname( $data, 'fulldesc', 'description' );

        $this->legacyFieldname( $data, 'laddress', 'event_address' );
        $this->legacyFieldname( $data, 'lcity', 'event_city' );
        $this->legacyFieldname( $data, 'lstate', 'event_state' );
        $this->legacyFieldname( $data, 'lzip', 'event_zip' );
        $this->legacyFieldname( $data, 'lcountry', 'event_country' );

        $this->legacyFieldname( $data, 'org', 'endorsers' );
        $this->legacyFieldname( $data, 'typeid', 'event_type_id' );

        $this->legacyFieldname( $data, 'contact1', 'public_contact_name' );
        $this->legacyFieldname( $data, 'email1', 'public_contact_email' );
        $this->legacyFieldname( $data, 'phone1', 'public_contact_phone' );

        $this->legacyFieldname( $data, 'lname2', 'Last_Name');
        $this->legacyFieldname( $data, 'fname2', 'First_Name');
        $this->legacyFieldname( $data, 'orgaznization2', 'Company');
        $this->legacyFieldname( $data, 'address2', 'Street');
        $this->legacyFieldname( $data, 'city2', 'City');
        $this->legacyFieldname( $data, 'state2', 'State');
        $this->legacyFieldname( $data, 'country2', 'Country');
        $this->legacyFieldname( $data, 'zip2', 'Zip');
        $this->legacyFieldname( $data, 'email2', 'Email');
        $this->legacyFieldname( $data, 'phone2', 'Work_Phone');

        $this->legacyFieldname( $data, 'fporder', 'front_page_order' );
        $this->legacyFieldname( $data, 'datestamp', 'entered_at' );
    }

    function getState( ) {
        return $this->getData( 'lstate' );
    }

    function getDate( ) {
        return $this->getData( 'date');
    }

    function _sort_default( &$item_set ) {
        $this->sort( $item_set, 'date', AMP_SORT_DESC );
    }

    function makeCriteriaEvent_type( $type_id ) {
        return "typeid=" . $type_id;
    }

    function makeCriteriaCurrent( $value ) {
        if ( $value === FALSE ) return TRUE;
        return "date >= CURRENT_DATE";
    }

    function makeCriteriaFront_page( $fpevent_value ) {
        if ( $fpevent_value === FALSE ) {
            return "TRUE";
        }
        return "fpevent=" && $fpevent_value;
    }

    function makeCriteriaState( $state_abbrev ) {
        $dbcon = AMP_Registry::getDbcon( );
        return "lstate = " . $dbcon->qstr( $state_abbrev );
    }

    function makeCriteriaRepeat( $value) {
        if ( $value === FALSE ) {
            return "TRUE";
        }
        return $this->_makeCriteriaEquals( 'repeat', $value );
    }

    function makeCriteriaStudent( $student_value ) {
        if ( $student_value === FALSE ) {
            return "TRUE";
        }
        return $this->_makeCriteriaEquals( 'student', $student_value );
    }

    function makeCriteriaDate ( $date_value ) {
        if ( !is_array( $date_value ) && $date_value ) {
            return "UNIX_TIMESTAMP( date ) = " . $date_value;
        }
        $partial_date_crit = array( );
        if ( isset( $date_value['Y']) && $date_value['Y'] ) {
            $partial_date_crit[] = 'YEAR( date ) = ' .$date_value['Y'];
        }
        if ( isset( $date_value['M']) && $date_value['M'] ) {
            $partial_date_crit[] = 'MONTH( date ) = ' . $date_value['M'];
        }
        if ( isset( $date_value['d']) && $date_value['d'] ) {
            $partial_date_crit[] = 'DAY( date ) = ' . $date_value['d'];
        }
        if ( empty( $partial_date_crit )) return 'TRUE';
        return join( ' AND ', $partial_date_crit );
    }

    function makeCriteriaArea( $state_id ) {
        $states_lookup = AMP_lookup( 'states') ;
        if ( !isset( $states_lookup[$state_id] )) return 'TRUE';
        $dbcon = AMP_Registry::getDbcon( );
        return '( lstate='. $dbcon->qstr( $states_lookup[ $state_id ]) . ' OR lstate= '.$state_id.')';
    }

    function getSection( ) {
        return $this->getData( 'section' );
    }

    function getURL( ) {
        return AMP_url_add_vars( AMP_CONTENT_URL_EVENT, array( 'id=' . $this->id ));
    }

    function makeCriteriaLive( ) {
        return 'publish=1';
    }

    function makeCriteriaOld( $value ) {
        if ( $value == 1 ) {
            return 'date <= CURRENT_DATE';
        }
        return 'TRUE';
    }

    function getShortLocation( ) {
        $basic_loc = $this->getData( 'lcity');
        $renderer = AMP_get_renderer( );
        $state = $this->getData( 'lstate' );
        $region_desc = false;
        if ( is_numeric( $state )) {
            $state_listing  = AMP_lookup( 'statenames');
            if ( !isset( $state_listing[ $state ])) {
                return $basic_loc;
            }
            $region_desc = $state_listing[ $states ];
        } elseif ( $state ) {
            $state_listing = AMP_lookup( 'regions_US_and_Canada');
            if ( isset( $state_listing['state'])) {
                $region_desc = $state_listing[ $state ];
            } else {
                $region_desc = $state;
            }
        }
        if ( !$region_desc || $region_desc == 'International') {
            $country = $this->getData( 'lcountry');
            $country_listing = AMP_lookup( 'regions_World');
            if ( isset( $country_listing[$country ])) {
                $region_desc = $country_listing[ $country ];
            } else {
                $region_desc = $country;
            }
        }
        return ucwords( $basic_loc . ','. $renderer->space( ) . $region_desc );
    }

    function getItemDate( ) {
        return $this->getData( 'date');
    }

    function getBlurb( ) {
        return $this->getData( 'shortdesc');
    }

    function getBody( ) {
        return $this->getData( 'fulldesc');
    }

    function getCountry( ) {
        return $this->getData( 'lcountry' );
    }

    function getCountryName( ) {
        if ( !( $result = $this->getCountry( ))) return false;
        $countries = AMP_lookup( 'regions_World');
        if ( !isset( $countries[ $result ])) {
            return $result;
        }
        return $countries[ $result ];
    }


	function export_keys() {
		$do_not_export = array( 'lname2', 'fname2', 'orgaznization2', 'address2', 'city2', 'state2', 'country2', 'zip2', 'email2', 'phone2', 'contact1', 'fulldesc', 'shortdesc', 'email1', 'phone1', 'endorse', 'lcity', 'lstate', 'enddate', 'lzip', 'lcountry', 'org', 'areaID', 'typeid', 'endtime', 'laddress' );
		$keys = parent::export_keys();
		return array_diff( $keys, $do_not_export );
    }


}

?>
