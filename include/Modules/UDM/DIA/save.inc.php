<?php

require_once( 'Modules/diaRequest.inc.php' );

function udm_DIA_save ( &$udm, $options = null ) {

/*	foreach ( $udm->fields as $fname => $fdef ) {
		$saveFields[] = $fname;
	} */

	$frmFieldValues = $udm->form->exportValues();

	$diaRequest = new diaRequest( $options[ 'orgCode' ] );
	$result = $diaRequest->addSupporter( $frmFieldValues[ 'Email' ], $frmFieldValues );

	return $result;

}

?>
