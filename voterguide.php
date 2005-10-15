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

	function VoterGuide_Controller(&$page) {
		$this->page = $page;
		$this->dbcon = AMP_Registry::getDbcon(); 
	}

	function execute() {
		if ( ( isset( $_GET['name']) && $short_name = $_GET['name']) && ( !isset( $_GET['id']) ) ) {
			$idByName = AMPSystem_Lookup::instance('VoterGuideByShortName');
			if(isset($idByName[$short_name])) {
				$_GET['id'] = $idByName[$short_name];
			}
		}

		if ( isset( $_GET['id']) && $_GET['id']) {
			$guide = &new VoterGuide( $this->dbcon, $_GET['id']);

			if ( isset( $_GET['action']) && $_GET['action'] == 'edit' ) {
				$udm = &new UserDataInput( $this->dbcon, AMP_FORM_ID_VOTERGUIDES );
				 
				$uid = (isset($_REQUEST['uid'])) ? $_REQUEST['uid'] : false;
				if(!$uid && $_REQUEST['id']) {
					$lookup = AMPSystem_Lookup::instance('OwnerByGuideID');
					$uid = $lookup[$_REQUEST['id']];
				}
				$otp = (isset($_REQUEST['otp'])) ? $_REQUEST['otp'] : null;

				$sub = isset($_REQUEST['btnUdmSubmit']) && $udm->formNotBlank();
				if ( $uid ) $auth = $udm->authenticate( $uid, $otp );
				if ( ( !$uid || $auth ) && $sub ) $udm->saveUser() ;
				if ( $uid && $auth && !$sub ) {
					$udm->submitted = false;
					$udm->getUser( $uid ); 
					$udm->registerPlugin('AMPVoterGuide','Save');
				}
				$mod_id = $udm->modTemplateID;
				AMP_directDisplay( $udm->output( ));

			} elseif ( isset( $_GET['action']) && $_GET['action'] == 'download' ) {
		//		print "Please be patient while we build your voter bloc";
				ampredirect('voterbloc.php?id='.$guide->id);
				
			} elseif ( isset( $_GET['action']) && $_GET['action'] == 'join' ) {
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
			} else {

				$this->page->contentManager->addDisplay( $guide->getDisplay() );
			}

		} elseif ( isset( $_GET['action']) && $_GET['action'] == 'new' ) {
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

		} else {
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

		
/*
		if(AMP_VOTERGUIDE_ACTION_EDIT == $this->getAction()) {
			$this->doEdit();
		} elseif(AMP_VOTERGUIDE_ACTION_JOIN == $this->getAction()) {
			$this->doJoin();
		}
*/
	}

	function doEdit() {
		return true;
	}

	function doJoin() {
		return true;
	}
}

if(defined('AMP_VOTERGUIDE_OLD_STYLE') && AMP_VOTERGUIDE_OLD_STYLE) {

if ( ( isset( $_GET['name']) && $short_name = $_GET['name']) && ( !isset( $_GET['id']) ) ) {
	$idByName = AMPSystem_Lookup::instance('VoterGuideByShortName');
	if(isset($idByName[$short_name])) {
		$_GET['id'] = $idByName[$short_name];
	}
}

if ( isset( $_GET['id']) && $_GET['id']) {
	$guide = &new VoterGuide( $dbcon, $_GET['id']);

	if ( isset( $_GET['action']) && $_GET['action'] == 'edit' ) {
		$udm = &new UserDataInput( $dbcon, AMP_FORM_ID_VOTERGUIDES );
		 
		$uid = (isset($_REQUEST['uid'])) ? $_REQUEST['uid'] : false;
		if(!$uid && $_REQUEST['id']) {
			$lookup = AMPSystem_Lookup::instance('OwnerByGuideID');
			$uid = $lookup[$_REQUEST['id']];
		}
		$otp = (isset($_REQUEST['otp'])) ? $_REQUEST['otp'] : null;

		$sub = isset($_REQUEST['btnUdmSubmit']) && $udm->formNotBlank();
		if ( $uid ) $auth = $udm->authenticate( $uid, $otp );
		if ( ( !$uid || $auth ) && $sub ) $udm->saveUser() ;
	    if ( $uid && $auth && !$sub ) {
			$udm->submitted = false;
			$udm->getUser( $uid ); 
			$udm->registerPlugin('AMPVoterGuide','Save');
	    }
		$mod_id = $udm->modTemplateID;
		AMP_directDisplay( $udm->output( ));

	} elseif ( isset( $_GET['action']) && $_GET['action'] == 'download' ) {
//		print "Please be patient while we build your voter bloc";
		ampredirect('voterbloc.php?id='.$guide->id);
		
	} elseif ( isset( $_GET['action']) && $_GET['action'] == 'join' ) {
		$_REQUEST['guide'] = $_GET['id'];
		$udm = &new UserDataInput( $dbcon, AMP_FORM_ID_VOTERBLOC );
		$sub = isset($_REQUEST['btnUdmSubmit']) && $udm->formNotBlank();
		if ( $uid ) $auth = $udm->authenticate( $uid, $otp );
		if ( ( !$uid || $auth ) && $sub ) $udm->saveUser() ;
		if ( $uid && $auth && !$sub ) {
			$udm->submitted = false;
			$udm->getUser( $uid ); 
		}
		$mod_id = $udm->modTemplateID;
		AMP_directDisplay( $udm->output( ));
	} else {

		$currentPage->contentManager->addDisplay( $guide->getDisplay() );
	}

} elseif ( isset( $_GET['action']) && $_GET['action'] == 'new' ) {
    $udm = &new UserDataInput( $dbcon, AMP_FORM_ID_VOTERGUIDES );
     
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

} else {
    $display = &new VoterGuideSet_Display( $dbcon );

    $searchForm = &new VoterGuideSearch_Form();
    $searchForm->Build( true );
    if ( $action = $searchForm->submitted( ) ) {
        $display->applySearch( $searchForm->getSearchValues() ); 
    } else {
        $searchForm->applyDefaults();

    }

    AMP_directDisplay( $searchForm->output() );
    $currentPage->contentManager->addDisplay( $display );
}

require_once( "AMP/BaseTemplate.php" );
require_once( "AMP/BaseModuleIntro.php" );
include("AMP/BaseFooter.php"); 
} //end defined old style
/*
function vg_postition($can,$pos,$reason=NULL) {
		$position = "<p><i>Candidate/Ballot Item</i>:&nbsp;<B>$can</b><br><i>Position:</i>&nbsp;<B>$pos</B><br><i>Reason:</i>&nbsp;$reason</p><BR>";
		return $position;
}

function vg_list($id,$state,$name,$city,$date,$intro) {
	$layout = "<p><span class='eventtitle'><a href='voterguide.php?detail=$id' class='vguidelink1'>$name</a><BR> <b>$city, $state<BR> $date</span> </b><br> $intro<br><center><span style='text-align: center; font-style:italic;'><a href='voterguide.php?detail=$id' class='vguidelink2'>view guide</a></span></center><P>&nbsp;<P>";
	return $layout;
}



function vg_detail($R) { 
	global $dbcon;
	$L= $dbcon->Execute("select * from voterguide where guide_id =".$_GET['detail']." ORDER BY textorder");
	echo "<P class=title>".$R->Fields("custom1")."<br><span class=subtitle>".$R->Fields("custom2").", ".$R->Fields("State").", ".$R->Fields("custom3")."</span></p>";
	//echo "<table width=\"100%\" border=\"0\"><tr><td align=\"right\">";
	if ($R->Fields("MI")) {echo "<a href ='downloads/".$R->Fields("MI")."'>Download the Voter Guide as a PDF</a><br>"; }
	else { echo "<a href=\"voterguide.php?detail=".$_GET[detail]."&printsafe=1\">Printer Safe Voter Guide</a><br>";}
	//echo "</td></tr></table>";
	if ($R->Fields("Suffix")) {echo "<br><a href ='modinput4.php?modin=".$R->Fields("Suffix")."' class=\"joinlink\">JOIN THIS VOTER BLOC -- ENDORSE THIS VOTER GUIDE!</a><br>"; }
	echo "<p>".$R->Fields("custom4")."</p><BR><BR>";
	if ($L->Fields("id") ) {
		while (!$L->EOF) {
			echo vg_postition($L->Fields("item"),$L->Fields("position"),$L->Fields("reason"));
		$L->MoveNext();
		}
	}
	if ($R->Fields("Suffix")) {echo "<a href ='modinput4.php?modin=".$R->Fields("Suffix")."' class=\"joinlink\">JOIN THIS VOTER BLOC -- ENDORSE THIS VOTER GUIDE!</a><br>"; }

}

if ($_GET[detail]) {
	$sql = "select * from userdata where id = ".$_GET['detail'];
	$d=$dbcon->CacheExecute("$sql")or DIE($dbcon->ErrorMsg());
	vg_detail($d);
}

else {
	if ($_GET['area']) {
		$st=$dbcon->CacheExecute("select state, statename from states where id = ".$_GET['area'])or DIE($dbcon->ErrorMsg());
		$where = " and State ='".$st->Fields("state")."'";
		$statetitle = "<p class=title>".$st->Fields("statename")."</p>";
	}
	$sql = "select id, State, custom1, custom2, custom3, custom4 from userdata where publish =1 and modin =52 $where order by State asc";
	$d=$dbcon->CacheExecute("$sql")or DIE($sql.$dbcon->ErrorMsg());
	echo $statetitle;
	while (!$d->EOF) {
		echo vg_list($d->Fields("id"),$d->Fields("State"),$d->Fields("custom1"),$d->Fields("custom2"),$d->Fields("custom3"),$d->Fields("custom4"),$d->Fields("custom5"));
		$d->MoveNext();
	}
	if ($d->RecordCount() ==0) {echo "<p>There are no voter guides in this area. Why don't you <a href=\"voterguide_add.php?modin=52\">create one</a>...?&nbsp;--&nbsp;&nbsp;<a href=\"article.php?id=34\">(how?)</a>";} 
	else {
		echo "<p><a href=\"voterguide_add.php?modin=52\">Create a local voter guide</a>&nbsp;--&nbsp;&nbsp;<a href=\"article.php?id=34\">(how?)</a></P>";
	}
}

 include("AMP/BaseFooter.php"); 
 */
?>
