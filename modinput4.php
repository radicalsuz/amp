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

// Set default module.
$mod_id = 57;
$modid = 1;

require_once( 'AMP/UserData/Input.inc.php' );
require_once( 'AMP/BaseDB.php' );

// Fetch the form instance specified by submitted modin value.
$udm = new UserDataInput( $dbcon, $_REQUEST[ 'modin' ] );
if (function_exists('_register_plugin_fields')) _register_plugin_fields($udm);

// User ID.
$uid = (isset($_REQUEST['uid'])) ? $_REQUEST['uid'] : false;
$otp = (isset($_REQUEST['otp'])) ? $_REQUEST['otp'] : null;

// Was data submitted via the web?
$sub = isset($_REQUEST['btnUdmSubmit']);

// Check fo rduplicates, setting $uid if found.
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

$mod_id = $udm->modTemplateID;

require_once( 'AMP/BaseTemplate.php' );
require_once( 'includes/moduleintro.php' );

print $udm->output();

// Append the footer and clean up.
require_once( 'AMP/BaseFooter.php' );

?>
