<?php

require_once( 'AMP/System/Data/Set.inc.php');
require_once( 'Modules/VoterGuide/Style/VoterGuide_Style.php');

class VoterGuide_StyleSet extends AMPSystem_Data_Set {
    var $datatable = 'voterguide_styles';
    var $sort = array( "name");

    function VoterGuide_StyleSet ( &$dbcon ){
        $this->init( $dbcon );
    }
}

?>
