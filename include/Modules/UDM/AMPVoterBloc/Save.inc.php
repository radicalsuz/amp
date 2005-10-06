<?php

require_once('Modules/VoterGuide/VoterGuide.php');
require_once('Modules/UDM/DIA/SupporterSave.inc.php');
require_once('AMP/Content/Page.inc.php');
require_once('Modules/VoterGuide/Lookups.inc.php');

class UserDataPlugin_Save_AMPVoterBloc extends UserDataPlugin_SupporterSave_DIA {

	function UserDataPlugin_Save_AMPVoterBloc(&$udm, $plugin_instance) {
		$this->init($udm, $plugin_instance);
	}

	function _register_fields_dynamic() {
		if(isset($_REQUEST['guide']) && $guide_id = $_REQUEST['guide']) {
			$this->fields['guide'] = array(
				'type' => 'hidden',
				'public' => true,
				'enabled' => true,
				'default' => $_REQUEST['guide'],
				'required' => false,
				'values' => $_REQUEST['guide']
			);

//use a lookup here
/*
			$guide =& new VoterGuide($this->dbcon, $guide_id);
			$bloc_id = $guide->getBlocID();
			$this->fields['bloc_id'] = array(
				'type' => 'hidden',
				'public' => true,
				'enabled' => true,
				'default' => $bloc_id,
				'required' => true,
				'values'  => $bloc_id
			);
*/
		} else {
			$guides = AMPSystem_Lookup::instance('LocationsNames');
			foreach($guides as $id => $guide_info) {
				list($state, $city, $name) = $guide_info;
				$select[$id] = sprintf("%-2s - %-10s - %-s", $state, substr($city, 0, 15), substr($name, 0, 10));
			}
			$this->fields['guide'] = array(
				'label' => 'Join which voter bloc?',
				'type' => 'select',
				'public' => true,
				'enabled' => true,
				'required' => false,
				'values' => $select
			);
		}
	}

    function getSaveFields() {
        return array_merge(parent::getSaveFields(), array('guide'));
    }

	function save($data) {
		$voter = parent::save($data);
		$currentPage =& AMPContent_Page::instance();

		$guide =& new VoterGuide($this->dbcon, $data['guide']);
		$currentPage->addObject(strtolower('UserDataPlugin_Save_AMPVoterGuide'), $guide);

		$results = VoterGuide::addVoterToBloc($voter, $guide->getBlocID());
		return true;
	}
}
?>
		
		

		
