<?php

require_once( 'AMP/System/Lookups.inc.php');

class WebAction_Lookup extends AMPSystem_Lookup {
    function WebAction_Lookup () {
        $this->init();
    }

    function &instance( $type, $lookup_baseclass="WebActionLookup" ) {
        return parent::instance( $type, $lookup_baseclass );
    }


}

class WebActionLookup_Targets extends WebAction_Lookup {
    var $datatable = 'webaction_targets';
    var $result_field = 'concat( First_Name, " ", Last_Name ) as Name';
    var $sortby = 'Last_Name, First_Name';

    function WebActionLookup_Targets( ){
        $this->init( );
    }
}

class WebActionLookup_Names extends WebAction_Lookup {
    var $datatable = 'webactions';
    var $result_field ='name';
    var $sortby = 'name';

    function WebActionLookup_Names( ){
        $this->init( );
    }
}
?>
