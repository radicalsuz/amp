<?php

require_once('Modules/VoterGuide/VoterGuide.php');
require_once('Modules/UDM/DIA/SupporterSave.inc.php');

class UserDataPlugin_Save_AMPVoterBloc extends UserDataPlugin_SupporterSave_DIA {

	function UserDataPlugin_Save_AMPVoterBloc(&$udm, $plugin_instance) {
		$this->init($udm, $plugin_instance);
	}

	function _register_fields_dynamic() {
		if(isset($_REQUEST['guide'])) {
			$guide =& new VoterGuide($this->dbcon, $_REQUEST['guide']);
			$bloc_id = $guide->getBlocID();
			$this->fields['bloc_id'] = array(
				'type' => 'hidden',
                'public' => true,
                'enabled' => true,
                'default' => $bloc_id,
                'required' => true,
                'values'  => $bloc_id
			);
		}
	}

	function getSaveFields() {
		return array_merge(parent::getSaveFields(), array('bloc_id'));
	}

	function save($data) {
		$voter = parent::save($data);
		return VoterGuide::addVoterToBloc($voter, $data['bloc_id']);
	}
}
?>
		
		

		
