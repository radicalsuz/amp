<?php

function udm_AMPlist_add_subscriber () {

	$dbcon = $udm->dbcon;
	$uid = $udm->uid;

	$email   = $udm->form->exportValues( 'EmailAddress' );

	$rs = $dbcon->Execute( "SELECT id FROM email WHERE email='$email'" );

	if ( $rs->RecordCount() == 0 ) {

		$fields = array( 'EmailAddress', 'LastName', 'FirstName', 
						 'Organization', 'html', 'Phone', 'WebPage',
						 'Address', 'Address2', 'City', 'State',
						 'PostcalCode', 'Country', 'Fax' );

		// Add user to email database.
		$sql  = "INSERT INTO email ( " . join( ", ", $fields ) . " ) VALUES ( '";
		$sql .= join( "', '", $udm->form->exportValues( $fields ) );

		$newRs = $dbcon->Execute( $sql );

		$uid = $db->Insert_ID();

	}

	foreach ( array_keys( $udm->lists ) as $listID ) {
		$listIDs = "listid" . $listID;
	}

	/* Remove all list subscriptions.
     *
	 * do this instead of selecting and finding which subscriptions
	 * need to be removed / added, since it means simpler code and
	 * negligible performance loss (or gain) for small sets of lists
	 *
	 */
	
	$sql  = "DELETE FROM subscription WHERE userid='" . $uid . "'";
	$sql .= " AND listid IN ( " . join( ", ", $listIDs ) . " )";

	$rs = $dbcon->Execute( $sql );

	$lists = $udm->form->exportValues( $listIDs );

	foreach ( $lists as $list_id => $subbed ) {

		// Skip if the user did not select the list.
		if ( !$subbed ) continue;
		
		$sql  = "INSERT INTO subscription ( recid, listid ) VALUES ";
		$sql .= "( '$uid', '$list_id' )";

		$rs = $dbcon->Execute( $sql );

	}

}

?>
