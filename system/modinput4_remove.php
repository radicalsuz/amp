<?php

require_once( 'Connections/freedomrising.php' );
require_once( 'header.php' );

if ( !isset( $_REQUEST['uid'] ) || !isset( $_REQUEST['modin'] ) ) {

?>

<h2>Delete User Data</h2>

<p>Please enter the ID of the record you would like to delete, or return to the <a href="moddata_list.php">list of records</a>.</p>

<form name="frmUserDelete" action="<?= $_SERVER['PHP_SELF'] ?>" method="POST">

	<label for="uid">User ID:</label>
	<input type="text" name="uid" />

	<label for="modin">Module ID:</label>
	<input type="text" name="modin" />

	<input type="submit" />

</form>

<?php 

} elseif ( !isset( $_REQUEST['confirm'] ) || $_REQUEST['confirm'] != 1 )  {

?>

<form name="frmUdmDeleteConf" action="<?= $_SERVER['PHP_SELF'] ?>" method="POST">

	<input type="hidden" name="modin" value="<?= $_REQUEST['modin'] ?>" />
	<input type="hidden" name="uid" value="<?= $_REQUEST['uid'] ?>" />

	<p>Are you sure you want to delete user #<?= $_REQUEST['uid'] ?>?</p>
	<label for="confirm">Yes</label>
	<input type="checkbox" name="confirm" value="1" />

	<br /><input type="submit" />

</form>

<?php


} elseif ( $_REQUEST['confirm'] == 1 ) {

	$sql = "DELETE FROM userdata WHERE id='" . $_REQUEST['uid'] . "'";
	$rs = $dbcon->Execute( $sql );

	if ( $rs ) {

		print "<p>User #" . $_REQUEST['uid'] . " was successfully deleted. Return to the <a href=\"modinput4_list.php\">list of user data modules</a>.</p>";

	} else {

		print "<p>There was an error deleting the selected user. Please contact your system administrator for assistance. The error message was: " . $dbcon->ErrorMsg() . "</p>";

	}
}

require_once( 'footer.php' );

?>
