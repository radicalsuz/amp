<?php

require_once( 'AMP/System/List.inc.php');
require_once( 'Modules/VoterGuide/Style/Set.inc.php' );

class VoterGuide_Style_List extends AMPSystem_List {
    var $name = "VoterGuide_Style";
    var $col_headers = array( 
        'name' => 'name',
        'ID'    => 'id');
    var $editlink = 'voterguide_styles.php';

    function VoterGuide_Style_List( &$dbcon ) {
        $source = & new VoterGuide_StyleSet( $dbcon );
        $this->init( $source );
    }
}
?>
