<?php

/*****
 *
 * AMP UserData Form View
 *
 * (c) 2004 Radical Designs
 * Written by Blaine Cook, blaine@radicaldesigns.org
 *
 *****/

//ob_start();
require_once( 'AMP/UserData/Input.inc.php' );
require_once( 'utility.functions.inc.php');

require("Connections/freedomrising.php");

$modidselect=$dbcon->Execute("SELECT id from modules where userdatamodid=".$_REQUEST['modin']) or DIE($dbcon->ErrorMsg());
$modid=$modidselect->Fields("id");

// Fetch the form instance specified by submitted modin value.
$udm = new UserDataInput( $dbcon, $_REQUEST[ 'modin' ], true );
$udm->doPlugin( "QuickForm", "BuildAdmin" );
$mod_id = $udm->modTemplateID;

// Was data submitted via the web?
$sub = (isset($_REQUEST['btnUdmSubmit']) && $_REQUEST['btnUdmSubmit']);

// Fetch or save user data.
if ( $sub ) {
    $udm->doPlugin( 'AMP', 'SaveAdmin' );
}

/* Now Output the Form.

   Any necessary changes to the form should have been registered
   before now, including any error messages, notices, or
   complete form overhauls. This can happen either within the
   $udm object, or from print() or echo() statements.

   By default, the form will include AMP's base template code,
   and any database-backed intro text to the appropriate module.

*/

include("header.php"); 

print "<h2>Add/Edit " . $udm->name . " Form</h2>";
print $udm->output();

include("footer.php"); 

?>