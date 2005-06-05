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
	
	// Was data submitted via the web?
	$sub = isset($_REQUEST['btnUdmSubmit']);
	
	
	// Fetch or save user data.
	if ( $sub ) {
		// Save only if submitted data is present, and the user is
		// authenticated, or if the submission is anonymous (i.e., !$uid)
		$udm->saveUser();
	} 
	
	//OUTPUT THE PAGE

	echo $P->progressBox();

	if ($_REQUEST["signers"]  or $_REQUEST['btnUdmSubmit']) {
		$udm->output();
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
	echo $P->petitionlist();
}

include_once("AMP/BaseFooter.php");
?>
