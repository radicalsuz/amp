<?php

function udm_amp_duplicate_check ( &$udm, $options = null ) {

	$dbcon =& $udm->dbcon;

	if ( !isset( $_REQUEST['Email'] ) || (strlen( $_REQUEST['Email'] ) < 5) )  {
		return false;
	} else {
		$email = $_REQUEST['Email'];
	}

	$sql = "SELECT id FROM userdata WHERE " .
		   " Email=" . $dbcon->qstr( $email );

	$rs = $dbcon->CacheExecute( $sql );

	if ( $rs ) {
		$row = $rs->FetchRow();
		return $row['id'];
	} else {
		return false;
	}

}

?>
