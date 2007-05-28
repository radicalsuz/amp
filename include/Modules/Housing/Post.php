<?php

require_once( 'AMP/System/Data/Item.inc.php');

class Housing_Post extends AMPSystem_Data_Item {
    var $datatable = 'userdata';
    var $_search_criteria_global = array( 'modin' => 'modin=11' );
    var $_class_name = 'Housing_Post';

    function Housing_Post( &$dbcon, $id = null ) {
        $this->__construct( $dbcon, $id );
    }

    function getType( ) {
        if ( $this->isType( AMP_TEXT_OFFER )) return ucwords( AMP_TEXT_OFFER );
        return ucwords( AMP_TEXT_REQUEST );
    }

    function isType( $type ) {
        $raw_type = $this->getRawType( );
        if ( $raw_type == 'Have Housing' ) {
            return ( $type == AMP_TEXT_OFFER );
        }
        if ( $raw_type == AMP_TEXT_OFFER ) {
            return ( $type == AMP_TEXT_OFFER );
        }
        return $type == AMP_TEXT_REQUEST;
    }

    function compare_type( $type ) {
        if ( ( $type == 'Have Housing') || ( $type == AMP_TEXT_OFFER ) ) {
            return AMP_TEXT_OFFER ;
        }
        if ( ( $type == 'Need Housing') || ( $type == AMP_TEXT_REQUEST ) ) {
            return AMP_TEXT_REQUEST ;
        }
        return AMP_TEXT_REQUEST;
    }

    function getRawType( ) {
        return $this->getData( 'custom1' );
    }

    function getLocation( ) {
        $location = array( );
        if ( $state = $this->getData( 'State')) {
            $location['state'] = $state;
        }

        if ( $city = $this->getData( 'City')) {
            $location['city'] = $city;
        }
        return join( ': ', $location );
    }

    function getName( ) {
        $lname = $this->getData('Last_Name');
        $fname = $this->getData('First_Name');
        if ( trim( $lname ) && trim( $fname )) {
            return $lname . ', ' .$fname;
        }
        return $lname . $fname;
    }

    function getAvailability( ) {
        return $this->getData( 'custom3' );
    }

    function getBaseLocation( ) {
        return $this->getData( 'custom8');
    }

    function getTransit( ) {
        return $this->getData( 'custom9');
    }

    function getParking( ) {
        return $this->getData( 'custom10');
    }

    function getMeals( ) {
        return $this->getData( 'custom11');
    }

    function getAccessibility( ) {
        return $this->getData( 'custom7');
    }

    function getBeds( ) {
        return $this->getData( 'custom4');
    }

    function getFloor( ) {
        return $this->getData( 'custom5');
    }

    function getTent( ) {
        return $this->getData( 'custom6');
    }

    function getSmoking( ){
        return $this->getData( 'custom14');
    }

    function getChildren( ){
        return $this->getData( 'custom13');
    }

    function getComments( ) {
        return $this->getData( 'custom18');
    }

    function _sort_default( &$item_set ) {
        $this->sort( $item_set, 'location' );
    }

    function getDatesRequested( ) {
        return $this->getData( 'custom16');
    }

    function getQtyRequested( ) {
        return $this->getData( 'custom17');
    }

    function makeCriteriaOffer( ) {
        return '( custom1="Have Housing" OR custom1="offer" )';
    }

    function makeCriteriaRequest( ) {
        return '( custom1="Need Housing" OR custom1="request" )';
    }

    function makeCriteriaType( $value ) {
        $base_type = $this->compare_type( $value );
        $crit_function = 'makeCriteria' . ucfirst( $base_type );
        return $this->$crit_function( );
    }

    function getLegacyType( ) {
        if ( $this->isType( AMP_TEXT_OFFER )) return 'Have Housing';
        return 'Need Housing';
    }

    function makeCriteriaLive( ) {
        return "( custom19=1 OR publish=1 )";
    }

    function makeCriteriaStatus( $value ) {
        if ( $value ) return $this->makeCriteriaLive( );
        if ( $value === 0 || $value === '0') return 'custom19!=1 && publish!=1';
    }

}


?>
