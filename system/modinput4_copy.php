<?php

/*****
 *
 * AMP UserData Copy Module
 *
 * (c) 2004 Radical Designs
 * Written by Blaine Cook, blaine@radicaldesigns.org
 *
 *****/

//ob_start();
require_once( 'AMP/UserDataInput.php' );
require_once( 'utility.functions.inc.php');
require("Connections/freedomrising.php");

// Fetch the form instance specified by submitted modin value.
$udm = new UserDataInput( $dbcon, $_REQUEST[ 'modin' ], true );
$udm->doPlugin( "QuickForm", "build_admin" );
if($new_modin=$udm->doPlugin( "AMP", "copy_admin" )) {
	header("Location: modinput4_edit.php?modin=".$new_modin);
} else {
	print "Sorry, the copy didn't work";
}

?>