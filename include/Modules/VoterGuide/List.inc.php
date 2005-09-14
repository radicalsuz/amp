<?php

require_once( 'AMP/System/List.inc.php' );
require_once( 'Modules/VoterGuide/VoterGuide.php' );
require_once( 'Modules/VoterGuide/Set.inc.php' );
#require_once( 'Modules/VoterGuide/Lookups.inc.php' );
#require_once( 'Modules/Schedule/Appointment/Set.inc.php' );

class VoterGuide_List extends AMPSystem_List {
    var $name = "VoterGuides";
    var $col_headers = array( "Name" => 'name', "City" => 'city', 'Status' => 'publish', 'Affiliate' => 'affiliation' );
    var $editlink = "voterguide.php";

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
