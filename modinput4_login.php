<?php

/*****
 *
 * AMP UserData Form View
 *
 * (c) 2004 Radical Designs
 * Written by Blaine Cook, blaine@radicaldesigns.org
 *
 *****/

ob_start();

require_once( 'AMP/UserDataInput.php' );
require_once( 'AMP/BaseDB.php' );

set_error_handler( 'e' );

// Fetch the form instance specified by submitted modin value.
$udm = new UserDataInput( $dbcon, $_REQUEST[ 'modin' ] );

$mod_id = $udm->modTemplateID;

require_once( 'AMP/BaseTemplate.php' );

if ( strlen( $_REQUEST['Email'] ) < 5 ) unset( $_REQUEST['Email'] );

// Check fo rduplicates, setting $uid if found.
if ( !$uid ) {

	$uid = $udm->findDuplicates();

}

// Check for authentication, sending authentication mail if necessary.
if ( $uid ) {

$oldSelf = $_SERVER['PHP_SELF'];
$_SERVER['PHP_SELF'] = "/modinput4.php";

    // Set authentication token if uid present
    $auth = $udm->authenticate( $uid );

$_SERVER['PHP_SELF'] = $oldSelf;

	?>

<p>
An email containing login information has been sent to <?= $_REQUEST['Email'] ?>. Follow the link contained in the email to login, or enter the password in the form below.
</p>

<form name="login" action="modinput4.php" method="GET">

	<input type="hidden" name="uid" value="<?= $uid ?>" />
	<input type="hidden" name="modin" value="<?= $udm->instance ?>" />

	<label for="otp">Password</label>
	<input type="text" size="40" name="otp" />

	<br />

	<input type="submit" value="Edit User Data" />

</form>

	<?php

} else {

	if ( isset( $_REQUEST['Email'] ) && ( strlen( $_REQUEST['Email'] ) > 5 ) ) {
		print "<p>Sorry, we were unable to find that email address in our records. Please check the address and try again.</p>";
	}

?>

<p>Please enter your email address, so that we can send you login information to edit your data.</p>

<form name="login" action="<?= $_SERVER['PHP_SELF'] ?>" method="POST">

	<input type="hidden" name="modin" value="<?= $udm->instance ?>" />

	<label for="Email">Email Address</label>
	<input type="text" size="40" name="Email" />

	<br />
	<input type="submit" value="Send Login Info" />

</form>

<?php

}

// Append the footer and clean up.
require_once( 'AMP/BaseFooter.php' );

?>
