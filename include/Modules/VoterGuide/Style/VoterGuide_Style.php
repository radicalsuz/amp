<?php

require_once( 'AMP/System/Data/Item.inc.php');

class VoterGuide_Style extends AMPSystem_Data_Item {

    var $datatable = "voterguide_styles";
    var $name_field = "name";

    function VoterGuide_Style ( &$dbcon, $id = null ) {
        $this->init( $dbcon, $id );
    }

}

?>
