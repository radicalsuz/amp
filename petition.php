<?php
##to do :
#  duplicat checking against email
#  email verifaction
# email dns lookup
# required fields
#  response pages 

# modinput2 needs to be changed to modinput 4 and not break

$modid = 7;
$mod_id = 42;
include_once("AMP/BaseDB.php");
include_once("AMP/BaseTemplate.php");
#include_once("AMP/BaseModuleIntro.php"); 
require_once( 'AMP/UserData/Input.inc.php' );
require_once( 'Modules/Petition/Petition.php' );

$pid = isset( $_REQUEST['pid']) && $_REQUEST['pid'] ? $_REQUEST['pid'] : false;
$modin = isset( $_REQUEST['modin']) && $_REQUEST['modin'] ? $_REQUEST['modin'] : false;
$current_petition = &new Petition( $dbcon, $pid, $modin );

if ($current_petition->id) {
	
	// Fetch the form instance specified by submitted modin value.
	$udm =& new UserDataInput( $dbcon, $current_petition->getFormId( ));
	
	// Was data submitted via the web?
	$sub = isset($_REQUEST['btnUdmSubmit']);
	
	
	// Fetch or save user data.
	if ( $sub ) {
		// Save only if submitted data is present, and the user is
		// authenticated, or if the submission is anonymous (i.e., !$uid)
		$udm->saveUser();
	} 
	
	//OUTPUT THE PAGE

	echo $current_petition->progressBox();

	if ($_REQUEST["signers"]  or $_REQUEST['btnUdmSubmit']) {
		$udm->output();
		echo $current_petition->petition_signers();
	}

	if(!$_REQUEST['btnUdmSubmit'] and (!$_REQUEST["signers"]) and  (!$_REQUEST["uid"])){
		echo $current_petition->intro_text();
		echo $current_petition->signature_link();	
		echo '<p class="title">Sign Petition</p>';
		print $udm->output();
	}	
	
} else {
	echo $current_petition->petitionlist();
}

include_once("AMP/BaseFooter.php");
?>
