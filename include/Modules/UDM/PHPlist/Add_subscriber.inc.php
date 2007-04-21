<?php

function udm_PHPlist_add_subscriber ( $udm, $options = array( )) {

	mt_srand( (double) microtime() * 1000000 );
	$rndVal = mt_rand();

	$dbcon = $udm->dbcon;

	$sql  = "INSERT INTO phplist_user_user (";
	$sql .= " email, confirmed, randval, htmlemail, entered ) VALUES ('";
	$sql .= $udm->form->exportValues( 'email' );
	$sql .= "', 1, '" . $rndVal . "', 1, NOW() )";

	$rs = $dbcon->Execute( $sql );

	if ( $rs ) {
		$dbcon->addMessage( "List subscriptions successfully processed." );
	} else {
		$dbcon->errorMessage( "There was an error processing list subscriptions." );
	}

	return $rs;

}

?>
