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
$intro_id = 57;
$modid = 1;

require_once( 'AMP/BaseDB.php' );
require_once( 'AMP/UserData/Input.inc.php' );

/**
 *  Check for a cached page
 */
if ( $cached_output = AMP_cached_request( )) {
    print $cached_output;
    exit;
}

$modin = ( isset( $_REQUEST['modin']) && $_REQUEST['modin']) ? $_REQUEST['modin'] : false;
if ( !$modin ) {
    $modin = ( isset( $_REQUEST['id']) && $_REQUEST['id']) ? $_REQUEST['id'] : false;
}

// Fetch the form instance specified by submitted modin value.
$udm =& new UserDataInput( $dbcon, $modin );

// User ID.
$uid = (isset($_REQUEST['uid'])) ? $_REQUEST['uid'] : false;
$otp = (isset($_REQUEST['otp'])) ? $_REQUEST['otp'] : null;

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
if ( ( !$uid || $auth ) && $udm->submitted ) {

    // Save only if submitted data is present, and the user is
    // authenticated, or if the submission is anonymous (i.e., !$uid)
    $udm->saveUser();

} elseif ( $uid && $auth && !$udm->submitted ) {

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

$intro_id = $udm->modTemplateID;

AMP_directDisplay( $udm->output());
require_once( 'AMP/BaseTemplate.php' );
require_once( 'AMP/BaseModuleIntro.php' );


// Append the footer and clean up.
require_once( 'AMP/BaseFooter.php' );

?>
