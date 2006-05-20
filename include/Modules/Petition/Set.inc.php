<?php

require_once( 'AMP/System/Data/Set.inc.php');
require_once( 'Modules/Petition/Petition.php');

class PetitionSet extends AMPSystem_Data_Set {
    var $datatable = 'petition';
    var $sort = array( "title");

    function PetitionSet ( &$dbcon ){
        $this->init( $dbcon );
    }
}

?>
