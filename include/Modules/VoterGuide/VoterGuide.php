<?php

class VoterGuide extends AMPSystem_Data_Item {

    var $datatable = 'voterguides';

    function VoterGuide ( &$dbcon, $id=null ) {
        $this->init( $dbcon, $id );
    }
}
?>