<?php

class VoterGuide_Position extends AMPSystem_Data_Item {

    var $datatable = 'voterguide_positions';
    var $name_field = "item";

    function VoterGuide_Position( &$dbcon, $id=null) {
        $this->init( $dbcon, $id );
    }

    function getTitle( ){
        return $this->getData( 'headline');
    }

    function getURL( ) {
        return false;
    }

    function getBlurb( ) {
        return $this->getData( 'comments' );
    }

    function getPosition( ) {
        return $this->getData( 'position' );
    }

    function getSubtitle( ) {
        return "Vote: " . $this->getPosition( ) . "  " . $this->getName( );
    }

}
?>
