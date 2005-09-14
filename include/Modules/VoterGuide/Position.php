<?php

class VoterGuide_Position extends AMPSystem_Data_Item {

    var $datatable = 'voterguide_positions';

    function VoterGuide_Position( &$dbcon, $id=null) {
        $this->init( $dbcon, $id );
    }
}
?>
