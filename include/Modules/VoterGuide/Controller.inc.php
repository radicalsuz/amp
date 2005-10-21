<?php
require_once( "AMP/BaseDB.php" );
require_once( "Modules/VoterGuide/ComponentMap.inc.php" );
require_once( "Modules/VoterGuide/Lookups.inc.php" );

require_once( "AMP/UserData/Input.inc.php" );
require_once( "AMP/Content/Map.inc.php" );
require_once( "AMP/Form/ElementCopierScript.inc.php" );
require_once( "Modules/VoterGuide/VoterGuide.php" );
require_once( "Modules/VoterGuide/Search/Form.inc.php" );
require_once( "Modules/VoterGuide/SetDisplay.inc.php" );

class VoterGuide_Controller {

	var $page;
	var $dbcon;
	var $udm;

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
		if(!defined('AMP_VOTERGUIDE_DEBUG')) {
			define('AMP_VOTERGUIDE_DEBUG', false);
		}

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
				
	function &getForm() {
		if(!isset($this->udm)) {
			$this->udm =& new UserDataInput( $this->dbcon, AMP_FORM_ID_VOTERGUIDES );
		}
		return $this->udm;
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
/*
		if($guide->isPublished()) {
			$this->page->contentManager->addDisplay( $guide->getDisplay() );
		}
*/
	}

	function getActionObjectOwner($object_id = null) {
		$uid = false;
		if(!isset($object_id) && !$this->action_id) {
			return false;
		} else {
			$object_id = $this->action_id;
		}

		$lookup = AMPSystem_Lookup::instance('OwnerByGuideID');
		$uid = $lookup[$object_id];

		return $uid;
	}

	function getUid() {
		return (isset($_REQUEST['uid'])) ? $_REQUEST['uid'] : false;
	}

	function getIntroID() {
		return $this->intro_id;
	}

	function getJoinURL($guide) {
		if(defined('AMP_VOTERGUIDE_JOIN_REDIRECT')) {
			return AMP_VOTERGUIDE_JOIN_REDIRECT.$guide->getShortName();
		}
		return 'voterguide.php?action=join&name='.$guide->getShortName();
	}

	function create_password() {
		return $this->login();
	}

	function reset_password() {
		return $this->login();
	}

	function login() {
		$auth = $this->authorized();
		if (!$auth) {
			//this should probably never happen
			$auth_plugin = $this->udm->getPlugin('AMPPassword','Authenticate');
			$auth_plugin->_handler->invalidate_cookie('Error', 'OK');
			$auth_plugin->_handler->do_logout();
			$redirect = 'voterguide.php?action=edit&id='.$this->action_id;
			$this->error('authenticate did not return a uid, redirecting to '.$redirect);
			return ampredirect($redirect);
		}

		//need to modify auth handling to account for other possible actions to pass to
		return $this->edit();
	}

	//am i logged in?
	//what userid owns this guide?
	//am i logged in as that userid?
	function authorized() {
		if(isset($this->authorized_user) && $this->authorized_user) {
			return $this->authorized_user;
		}
		$udm =& $this->getForm();
		$guide =& $this->getActionObject();
		$owner = $guide->getOwner();
		$one_time_password = $this->getOtp();
		$_REQUEST['uid'] = $owner;
		$authorized_user = $udm->authenticate( $owner, $one_time_password );
		return $this->authorized_user = $authorized_user;
	}

	function getOtp() {
		return (isset($_REQUEST['otp'])) ? $_REQUEST['otp'] : null;
	}

	function edit() {
		if(!$this->authorized()) {
			//should never happen, since udm auth assumes control
			return $this->login();
		}

		$sub = isset($_REQUEST['btnUdmSubmit']) && $this->udm->formNotBlank();

		if ( $sub ) {
			$this->udm->saveUser() ;
		} else {
			$guide =& $this->getActionObject();
			if(defined('AMP_VOTERGUIDE_EDIT_HEADER')) {
				$this->page->addObject(strtolower('UserDataPlugin_Save_AMPVoterGuide'), $guide);
//				$this->page->setIntroText(AMP_VOTERGUIDE_EDIT_HEADER);
				$this->intro_id = AMP_VOTERGUIDE_EDIT_HEADER;
			}
			$this->udm->submitted = false;
			$this->udm->getUser( $uid ); 

//			$udm->registerPlugin('AMPVoterGuide', 'Save');
			$save =& $this->udm->getPlugin('AMPVoterGuide','Save');
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

			$this->udm->setData($data);

//			$udm->setData($guide->getData());
//			$copier =& ElementCopierScript::instance();
//			$copier->addSets('voterguidePositions', $guide->_positionSet->getArray());
		}
		$mod_id = $this->udm->modTemplateID;
		AMP_directDisplay( $this->udm->output( ));
	}

	function join() {
		$guide =& $this->getActionObject();
		$this->page->addObject(strtolower('UserDataPlugin_Save_AMPVoterGuide'), $guide);
		
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
		$this->intro_id = $mod_id;
		AMP_directDisplay( $udm->output( ));
	}

	function unsubscribe() {
		$email = $_REQUEST['Email'];

		$guide =& $this->getActionObject();
		$guide->removeVoterFromBloc($email);
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

	function error($message, $level = E_USER_WARNING) {
		if(defined('AMP_VOTERGUIDE_DEBUG') && AMP_VOTERGUIDE_DEBUG) {
			trigger_error($message, $level);
		}
		$this->errors[] = $message;
	}

	function notice($message) {
		$this->error($message, E_USER_NOTICE);
	}
}

?>
