<?php
require_once( "AMP/BaseDB.php" );
require_once( "Modules/VoterGuide/ComponentMap.inc.php" );
require_once( "Modules/VoterGuide/Lookups.inc.php" );

class VoterGuide_Controller {

	var $page;
	var $dbcon;

	var $action_method = null;
	var $action_id = null;
	var $action_object = null;

	var $protected_methods;

	var $intro_id;

	function VoterGuide_Controller(&$page) {
		$this->page =& $page;
		$this->dbcon = AMP_Registry::getDbcon(); 
		$this->init();
	}

	function init() {
		$this->setProtectedMethods();
		$this->action_method = (isset($_GET['action']) && $_GET['action'])?
							$_GET['action']:null;
		if('new' == $this->action_method) {
			$this->action_method = 'post';
		}

		if ( isset( $_GET['id']) && $_GET['id']) {
			$this->action_id = $_GET['id'];
		} elseif ( ( isset( $_GET['name']) && $short_name = $_GET['name']) ) {
			$idByName = AMPSystem_Lookup::instance('VoterGuideByShortName');
			if(isset($idByName[$short_name])) {
				$this->action_id = $idByName[$short_name];
			}
		}

		if( !isset($this->action_method) ) {
			if(isset($this->action_id)) {
				$this->action_method = 'view';
			} else {
				$this->action_method = 'search';
			}
		}

	}

	function execute() {

		return $this->doAction();

	}

	function setProtectedMethods() {
	}

	function &getActionObject() {
		if(!isset($this->action_object)) {
			if(!isset($this->action_id)) {
				return false;
			} else {
				$this->action_object =& new VoterGuide($this->dbcon, $this->action_id);
			}
		}
		return $this->action_object;
	}
				
				
	function allowed($action) {
		return method_exists($this, $action) && !$this->protected_methods[$action];
	}

	function doAction() {
		$action = $this->action_method;
		if(isset($action) && $action && $this->allowed($action)) {
			$this->$action();
			return true;
		}
		return false;
	}

	function view() {
		$guide =& $this->getActionObject();
		$this->page->contentManager->addDisplay( $guide->getDisplay() );
	}

	function getUid($guide_id = null) {
		$uid = false;
		$object_id = isset($guide_id)?$guide_id:$this->action_id;
		if($object_id) {
			$lookup = AMPSystem_Lookup::instance('OwnerByGuideID');
			$uid = $lookup[$object_id];
		}
		if(!$uid) {
			$uid = (isset($_REQUEST['uid'])) ? $_REQUEST['uid'] : false;
		}
		return $uid;
	}

	function getIntroID() {
		return $this->intro_id;
	}

	function create_password() {
		return $this->create();
	}

	function reset() {
		return $this->create();
	}

	function create() {
		$udm = &new UserDataInput( $this->dbcon, AMP_FORM_ID_VOTERGUIDES );
		$uid = $this->getUid($this->action_id);
		$otp = (isset($_REQUEST['otp'])) ? $_REQUEST['otp'] : null;
		if ( $uid ) $auth = $udm->authenticate( $uid, $otp );
		if (!$auth) {
			return $this->edit();
		}
		return $this->edit();	
	}
		
	function edit() {
		$udm = &new UserDataInput( $this->dbcon, AMP_FORM_ID_VOTERGUIDES );
		 
		$uid = $this->getUid();
		$otp = (isset($_REQUEST['otp'])) ? $_REQUEST['otp'] : null;

		$sub = isset($_REQUEST['btnUdmSubmit']) && $udm->formNotBlank();
		if ( $uid ) $auth = $udm->authenticate( $uid, $otp );
		$guide =& $this->getActionObject();
//		$udm->uid = $uid;
//trigger_error("authenticate returned: $auth", E_USER_WARNING);
		if ( ( !$uid || $auth ) && $sub ) $udm->saveUser() ;
		if ( $uid && $auth && !$sub ) {
			if(defined('AMP_VOTERGUIDE_EDIT_HEADER')) {
				$this->page->addObject(strtolower('UserDataPlugin_Save_AMPVoterGuide'), $guide);
				$this->page->setIntroText(AMP_VOTERGUIDE_EDIT_HEADER);
			}
			$udm->submitted = false;
			$udm->getUser( $uid ); 

//			$udm->registerPlugin('AMPVoterGuide', 'Save');
			$save =& $udm->getPlugin('AMPVoterGuide','Save');
			$guide->readData($guide->id);
			$positions = $guide->getData($save->_copierName);
			$prefix = $save->_copier->getPrefix($save->_copierName);
			foreach($positions as $position) {
				foreach($position as $field => $value) {
					$set[$prefix.'_'.$field][] = $value;
				}
			}
			$save->_copier->addSets($save->_copierName, $set);
			$save->_register_javascript($save->_copier->output());

			$fields = $save->_guideForm->getFields();
			foreach($guide->getData() as $property => $value) {
				$data[$save->_field_prefix.'_'.$property] = $value;
			}
			$data[$save->_field_prefix.'_accurate_checkbox']['default'] = 'checked';
			$data[$save->_field_prefix.'_trust_checkbox']['default'] = 'checked';

			$udm->setData($data);

//			$udm->setData($guide->getData());
//			$copier =& ElementCopierScript::instance();
//			$copier->addSets('voterguidePositions', $guide->_positionSet->getArray());
		}
		$mod_id = $udm->modTemplateID;
		$this->intro_id = $mod_id;
		AMP_directDisplay( $udm->output( ));
	}

	function join() {
		$_REQUEST['guide'] = $this->action_id;
		$udm = &new UserDataInput( $this->dbcon, AMP_FORM_ID_VOTERBLOC );
		$sub = isset($_REQUEST['btnUdmSubmit']) && $udm->formNotBlank();
		if ( $uid ) $auth = $udm->authenticate( $uid, $otp );
		if ( ( !$uid || $auth ) && $sub ) $udm->saveUser() ;
		if ( $uid && $auth && !$sub ) {
			$udm->submitted = false;
			$udm->getUser( $uid ); 
		}
		$mod_id = $udm->modTemplateID;
		AMP_directDisplay( $udm->output( ));
	}

//	function create() {
	function post() {
		$udm = &new UserDataInput( $this->dbcon, AMP_FORM_ID_VOTERGUIDES );
		 
		$uid = (isset($_REQUEST['uid'])) ? $_REQUEST['uid'] : false;
		$otp = (isset($_REQUEST['otp'])) ? $_REQUEST['otp'] : null;

		$sub = isset($_REQUEST['btnUdmSubmit']) && $udm->formNotBlank();
		if ( $uid ) $auth = $udm->authenticate( $uid, $otp );
		if ( ( !$uid || $auth ) && $sub ) $udm->saveUser() ;
		if ( $uid && $auth && !$sub ) {
			$udm->submitted = false;
			$udm->getUser( $uid ); 
		}
		$mod_id = $udm->modTemplateID;
		$this->intro_id = $mod_id;
		AMP_directDisplay( $udm->output( ));
	}

	function download() {
//		print "Please be patient while we build your voter bloc";
		ampredirect('voterbloc.php?id='.$this->action_id);
	}

	function search() {
		$display = &new VoterGuideSet_Display( $this->dbcon );

		$searchForm = &new VoterGuideSearch_Form();
		$searchForm->Build( true );
		if ( $action = $searchForm->submitted( ) ) {
			$display->applySearch( $searchForm->getSearchValues() ); 
		} else {
			$searchForm->applyDefaults();

		}

		AMP_directDisplay( $searchForm->output() );
		$this->page->contentManager->addDisplay( $display );
	}
}

?>
