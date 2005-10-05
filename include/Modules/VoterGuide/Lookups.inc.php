<?php
require_once( 'AMP/System/Lookups.inc.php' );

class AMPSystemLookup_VoterGuideNames extends AMPSystem_Lookup {

    var $datatable = "voterguides";
    var $result_field = "name";

    function AMPSystemLookup_VoterGuideNames() {
        $this->init();
    }
}

class AMPSystemLookup_VoterGuideByBlocID extends AMPSystem_Lookup {
    var $datatable = "voterguides";
    var $result_field = "id";
	var $id_field = "bloc_id";

    function AMPSystemLookup_VoterGuideByBlocID() {
        $this->init();
    }
}

class AMPSystemLookup_VoterGuideByBlocIDInPost extends AMPSystem_Lookup {
    var $datatable = "voterguides";
    var $result_field = "id";
	var $id_field = "bloc_id";

    function AMPSystemLookup_VoterGuideByBlocID() {
		$this->criteria = "where bloc_id = " . $_REQUEST['bloc_id'];
        $this->init();
    }
}
?>
