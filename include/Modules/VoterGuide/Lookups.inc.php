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

    function AMPSystemLookup_VoterGuideByBlocID($bloc_id) {
        $this->init();
    }
}

class AMPSystemLookup_VoterGuideByBlocIDInPost extends AMPSystem_Lookup {
    var $datatable = "voterguides";
    var $id_field = "id";

    function AMPSystemLookup_VoterGuideByBlocID() {
		$this->criteria = "bloc_id = " . $_REQUEST['bloc_id'];
        $this->init();
    }

	function init() {
		parent::init();
		$this->dataset = $this->dataset[0];
	}
}

class AMPSystemLookup_LocationsNames extends AMPSystem_Lookup {
	var $datatable = "voterguides";
	var $id_field = "id";
	var $result_field = "state, city, name";
	var $sortby = "state, city, name";
}

class AMPSystemLookup_voterguidesByShortName extends AMPSystem_Lookup {
	var $datatable = "voterguides";
	var $id_field = "short_name";
	var $result_field = "id";
}

class AMPSystemLookup_OwnerByGuideID extends AMPSystem_Lookup {
	var $datatable = "voterguides";
	var $id_field = "id";
	var $result_field = "owner_id";
}

?>
