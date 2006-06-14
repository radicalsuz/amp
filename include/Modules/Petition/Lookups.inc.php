<?php

require_once( 'AMP/System/Lookups.inc.php' );

class AMPSystemLookup_PetitionsByModin extends AMPSystem_Lookup {
    var $id_field = 'udmid';
    var $result_field = 'id';
    var $criteria = '!isnull( udmid ) and udmid != ""';

    function AMPSystemLookup_PetitionsByModin( ){
        $this->init( );
    }
}

?>
