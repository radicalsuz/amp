<?php

require_once( 'AMP/System/List/Form.inc.php' );
require_once( 'Modules/VoterGuide/VoterGuide.php' );
require_once( 'Modules/VoterGuide/Set.inc.php' );
#require_once( 'Modules/VoterGuide/Lookups.inc.php' );
#require_once( 'Modules/Schedule/Appointment/Set.inc.php' );

class VoterGuide_List extends AMP_System_List_Form {
    var $name = "VoterGuides";
    var $col_headers = array( "Guide Name" => 'name', "City" => 'city', 'Status' => 'publish', 'Affiliate' => 'affiliation', 'Cycle' => 'election_cycle' );
    var $editlink = "voterguide.php";
    var $_source_object = 'VoterGuide';

    function VoterGuide_List( &$dbcon, $id = null ) {
        $source = & new VoterGuideSet( $dbcon );
        $this->init( $source );
    }

    function getGuidesByOwner( $userdata_id ) {
        $this->source->addCriteria( "owner_id" . "=" . $userdata_id );
        $this->source->readData();
    }

}
?>
