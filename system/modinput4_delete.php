<?php

require_once( 'Connections/freedomrising.php' );
require_once( 'header.php' );

if ( !isset( $_REQUEST['modin'] ) ) {

?>

<h2>Delete User Data Module</h2>

<p>Please enter the ID of the module you would like to delete, or return to the <a href="modinput4_list.php">list of available user data modules</a>.</p>

<form name="frmUdmDelete" action="<?= $_SERVER['PHP_SELF'] ?>" method="POST">

	<label for="modin">Module Instance ID:</label>
	<input type="text" name="modin" />

	<input type="submit" />

</form>

<?php 

} elseif ( !isset( $_REQUEST['confirm'] ) || $_REQUEST['confirm'] != 1 )  {

?>

<form name="frmUdmDeleteConf" action="<?= $_SERVER['PHP_SELF'] ?>" method="POST">

	<input type="hidden" name="modin" value="<?= $_REQUEST['modin'] ?>" />

	<p>Are you sure you want to delete module #<?= $_REQUEST['modin'] ?>?</p>
	<label for="confirm">Yes</label>
	<input type="checkbox" name="confirm" value="1" />

	<br /><input type="submit" />

</form>

<?php


} elseif ( $_REQUEST['confirm'] == 1 ) {

	$sql = "DELETE FROM userdata_fields WHERE id='" . $_REQUEST['modin'] . "'";
	$rs = $dbcon->Execute( $sql );

	if ( $rs ) {

		print "<p>Module #" . $_REQUEST['modin'] . " was successfully deleted. Return to the <a href=\"modinput4_list.php\">list of user data modules</a>.</p>";

	} else {

		print "<p>There was an error deleting the selected module. Please contact your system administrator for assistance. The error message was: " . $dbcon->ErrorMsg() . "</p>";

	}
}

require_once( 'footer.php' );

?>
