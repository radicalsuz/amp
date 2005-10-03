<?php

class VoterGuide_Position extends AMPSystem_Data_Item {

    var $datatable = 'voterguide_positions';
    var $name_field = "item";

    var $_votes = array( 'Hell Yeah', 'Yeah', 'No', 'No Way', 'No Endorsement');

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

    function getPositionValue( ) {
        return $this->getData( 'position' );
    }

    function getVoteSet( ) {
        return $this->_votes;
    }

    function translateVote( $vote_key ) {
        if ( !isset( $this->_votes[$vote_key])) return false;
        return $this->_votes[$vote_key];
    }

    function getPosition( ) {
        $position = $this->getPositionValue();
        if ( $position !== false ) return $this->translateVote( $position );
        return false;
    }

    function getSubtitle( ) {
        $output = "";
    }

}
?>
