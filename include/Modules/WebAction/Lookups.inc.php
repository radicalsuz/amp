<?php

require_once( 'AMP/System/Lookups.inc.php');

class WebAction_Lookup extends AMPSystem_Lookup {
    function WebAction_Lookup () {
        $this->init();
    }

    function &instance( $type, $instance_var = null, $lookup_baseclass="WebActionLookup" ) {
        return parent::instance( $type, $instance_var, $lookup_baseclass );
    }


}

class AMPSystemLookup_ActionTargets extends AMPSystem_Lookup {
    function AMPSystemLookup_ActionTargets( ) {
        $this->dataset = WebAction_Lookup::instance( 'targets' );
    }
}

class AMPSystemLookup_WebActions extends AMPSystem_Lookup {
    function AMPSystemLookup_WebActions {
        $this->dataset = WebAction_Lookup::instance( 'names' );
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
