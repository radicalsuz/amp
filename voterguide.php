<?php

$modid = 109;
$mod_id = 74;

define('AMP_VOTERGUIDE_OLD_STYLE', false);

require_once( "AMP/BaseDB.php" );
require_once( "AMP/UserData/Input.inc.php" );
require_once( "AMP/Content/Page.inc.php" );
require_once( "AMP/Content/Map.inc.php" );
require_once( "Modules/VoterGuide/ComponentMap.inc.php" );
require_once( "Modules/VoterGuide/Lookups.inc.php" );
require_once( "AMP/Form/ElementCopierScript.inc.php" );


require_once( "Modules/VoterGuide/VoterGuide.php" );
require_once( "Modules/VoterGuide/Search/Form.inc.php" );
require_once( "Modules/VoterGuide/SetDisplay.inc.php" );

$currentPage = &AMPContent_Page::instance();

$voterguide =& new VoterGuide_Controller($currentPage);
$voterguide->execute();

require_once( "AMP/BaseTemplate.php" );
require_once( "AMP/BaseModuleIntro.php" );
include("AMP/BaseFooter.php"); 

class VoterGuide_Controller {

	var $page;
	var $dbcon;

	var $action = null;
	var $action_id = null;

	var $protected_methods;

	function VoterGuide_Controller(&$page) {
		$this->page = $page;
		$this->dbcon = AMP_Registry::getDbcon(); 
		$this->init();
	}

	function init() {
		$this->setProtectedMethods();
		$this->action = (isset($_GET['action']) && $_GET['action'])?
							$_GET['action']:null;
		if('new' == $this->action || 'post' == $this->action) {
			$this->action = 'create';
		}

		if ( isset( $_GET['id']) && $_GET['id']) {
			$this->action_id = $_GET['id'];
		} elseif ( ( isset( $_GET['name']) && $short_name = $_GET['name']) ) {
			$idByName = AMPSystem_Lookup::instance('VoterGuideByShortName');
			if(isset($idByName[$short_name])) {
				$this->action_id = $idByName[$short_name];
			}
		}
	}

	function execute() {

		if ( isset($this->action_id) && $this->action_id ) {
			$guide = &new VoterGuide( $this->dbcon, $this->action_id);

			$this->doAction($guide) or $this->view($guide);

		} elseif ( isset( $_GET['action']) && $_GET['action'] == 'new' ) {
			$this->create();	
		} else {
			$this->search();
		}
	}

	function setProtectedMethods() {
	}

	function can($action) {
		return method_exists($this, $action) && !$this->protected_methods[$action];
	}

	function doAction(&$guide) {
		$action = $this->action;
		if(isset($action) && $action && $this->can($action)) {
			$this->$action($guide);
			return true;
		}
		return false;
	}

	function view(&$guide) {
		$this->page->contentManager->addDisplay( $guide->getDisplay() );
	}

	function edit(&$guide) {
		$udm = &new UserDataInput( $this->dbcon, AMP_FORM_ID_VOTERGUIDES );
		 
		$uid = (isset($_REQUEST['uid'])) ? $_REQUEST['uid'] : false;
		if(!$uid && $_REQUEST['id']) {
			$lookup = AMPSystem_Lookup::instance('OwnerByGuideID');
			$uid = $lookup[$_REQUEST['id']];
		}
		$otp = (isset($_REQUEST['otp'])) ? $_REQUEST['otp'] : null;

		$sub = isset($_REQUEST['btnUdmSubmit']) && $udm->formNotBlank();
//		if ( $uid ) $auth = $udm->authenticate( $uid, $otp );
//		if ( ( !$uid || $auth ) && $sub ) $udm->saveUser() ;
//		if ( $uid && $auth && !$sub ) {
		if ( true ) {
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
			$udm->setData($data);

//			$udm->setData($guide->getData());
//			$copier =& ElementCopierScript::instance();
//			$copier->addSets('voterguidePositions', $guide->_positionSet->getArray());
		}
		$mod_id = $udm->modTemplateID;
		AMP_directDisplay( $udm->output( ));
	}

	function join(&$guide) {
		$_REQUEST['guide'] = $_GET['id'];
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

	function create() {
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
		AMP_directDisplay( $udm->output( ));
	}

	function download(&$guide) {
//		print "Please be patient while we build your voter bloc";
		ampredirect('voterbloc.php?id='.$guide->id);
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
