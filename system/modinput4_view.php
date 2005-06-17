<?php

/*****
 *
 * AMP UserData Form View
 *
 * (c) 2004 Radical Designs
 * Written by Blaine Cook, blaine@radicaldesigns.org
 *
 *****/
$mod_name='udm';
require_once( 'AMP/UserData/Input.inc.php' );
require_once( 'Connections/freedomrising.php' );
require_once( 'utility.functions.inc.php' );

#set_error_handler( 'e' );
$admin = $userper[54]; //UDM All permission

// Fetch the form instance specified by submitted modin value.
$udm = &new UserDataInput( $dbcon, $_REQUEST[ 'modin' ],$admin );



$modidselect = $dbcon->Execute("SELECT id, perid from modules where userdatamodid=" . $dbcon->qstr($udm->instance) )
                or die("Couldn't get module information for form: " . $dbcon->ErrorMsg());
$modid = $modidselect->Fields("id");
$modin_permission = $modidselect->Fields("perid");

// User ID.
$uid = (isset($_REQUEST['uid'])) ? $_REQUEST['uid'] : false;
$udm->authorized = true;
$udm->uid = $uid;

// Was data submitted via the web?
$sub = (isset($_REQUEST['btnUdmSubmit'])) ? $_REQUEST['btnUdmSubmit'] : false;


// Fetch or save user data.
if ( $sub ) {

    // Save only if submitted data is present, and the user is
    // authenticated, or if the submission is anonymous (i.e., !$uid)
	if($udm->saveUser()) {
			header ("Location:modinput4_data.php?modin=".$udm->instance);
	}
	$udm->showForm = true;

} elseif ( !$sub && $uid ) {

    // Fetch the user data for $uid if there is no submitted data
    // and the user is authenticated.
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

$mod_id = $udm->modTemplateID;

require_once( 'header.php' );

print "<h2>Add/Edit " . $udm->name . "</h2>";
print "<font color = \"red\">".$udm->outputErrors()."</font>";
print $udm->output();

// Append the footer and clean up.
require_once( 'footer.php' );

?>
