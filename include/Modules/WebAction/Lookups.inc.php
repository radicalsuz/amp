<?php

require_once( 'AMP/System/Lookups.inc.php');

class WebAction_Lookup extends AMPSystem_Lookup {
    function WebAction_Lookup () {
        $this->init();
    }

    function &instance( $type, $lookup_baseclass="WebActionLookup" ) {
        return PARENT::instance( $type, $lookup_baseclass );
    }


}

class WebActionLookup_Targets extends WebAction_Lookup {
    var $datatable = 'action_targets';
    var $result_field = 'concat( firstname, " ", lastname ) as name';
    var $sortby = 'lastname, firstname';

    function WebActionLookup_Targets( ){
        $this->init( );
    }
}

class WebActionLookup_Names extends WebAction_Lookup {
    var $datatable = 'webactions';
    var $result_field ='title';
    var $sortby = 'title';

    function WebActionLookup_Names( ){
        $this->init( );
    }
}
?>
