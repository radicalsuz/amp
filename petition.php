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

$P = new Petition( $dbcon, $_REQUEST['pid'], $_REQUEST['modin'] );

if ($P->pid) {
	
	// Fetch the form instance specified by submitted modin value.
	$udm =& new UserDataInput( $dbcon, $P->petmod );
	
	// User ID.
	$uid = (isset($_REQUEST['uid'])) ? $_REQUEST['uid'] : false;
	$otp = (isset($_REQUEST['otp'])) ? $_REQUEST['otp'] : null;
	
	// Was data submitted via the web?
	$sub = isset($_REQUEST['btnUdmSubmit']);
	
	// Check for duplicates, setting $uid if found.
	if ( !$uid ) {
	
		$uid = $udm->findDuplicates();
	
	} 
	
	// Check for authentication, sending authentication mail if necessary.
	if ( $uid ) {
	
		// Set authentication token if uid present
		$auth = $udm->authenticate( $uid, $otp );
	
	}
	
	// Fetch or save user data.
	if ( ( !$uid || $auth ) && $sub ) {
	
		// Save only if submitted data is present, and the user is
		// authenticated, or if the submission is anonymous (i.e., !$uid)
		$udm->saveUser();
	} elseif ( $uid && $auth && !$sub ) {
	
		// Fetch the user data for $uid if there is no submitted data
		// and the user is authenticated.
		$udm->submitted = false;
		$udm->getUser( $uid ); 
	
	}
	
	/* Now Output the Form.
	
	   Any necessary changes to the form should have been registered
	   before now, including any error messages, notices, or
	   complete form overhauls. This can happen either within the
	   $udm object, or from print() or echo() statements.
	
	   By default, the form will include AMP's base template code,
	   and any database-backed intro text to the appropriate module.
	
	*/
	
	
	//OUTPUT THE PAGE

	echo $P->progressBox();

	if ($_REQUEST["signers"]  or $_REQUEST['btnUdmSubmit']) {
		echo $P->petition_signers();
	}

	if(!$_REQUEST['btnUdmSubmit'] and (!$_REQUEST["signers"]) and  (!$_REQUEST["uid"])){
		echo $P->intro_text();
		echo $P->signature_link();	
		echo '<p class="title">Sign Petition</p>';
		print $udm->output();
	}	
	
}
else {
	//echo $P->petitionlist();
}

include_once("AMP/BaseFooter.php");
?>
