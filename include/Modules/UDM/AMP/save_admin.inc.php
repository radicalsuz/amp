<?php

function udm_amp_save_admin ( $udm, $options = null ) {

	$dbcon = $udm->dbcon;

    $udm->doPlugin( 'AMP', 'fixup_db' );

	// Insert or Update?
	if (isset( $udm->instance )) {

		$sql = "UPDATE userdata_fields SET ";

		$frmFieldValues = array_keys( $udm->_module_def );
		foreach ( $frmFieldValues as $field ) {
			if ( substr( $field, 0, 5 ) == "core_" ) $skipFields[] =  substr( $field, 5 );
		}

		foreach ( $frmFieldValues as $field ) {

			if ( array_search( $field, $skipFields ) ) continue;

			$sql_field = $field;

			if ( $field == 'id' ) continue;
			if ( substr( $field, 0, 5 ) == "core_" ) $sql_field = substr( $field, 5 );

			$elements[] = $sql_field . "=" . $dbcon->qstr( $udm->form->getSubmitValue( $field ) );

		}

		$sql .= join( ", ", $elements );
		$sql .= " WHERE id=" . $dbcon->qstr( $udm->instance );

	} else {

		$sql = "INSERT INTO userdata (";
		$frmFieldValues = $udm->form->ExportValues( array_keys( $udm->fields ) );

		$fields = array_keys( $frmFieldValues );
		$values_noescape = array_values( $frmFieldValues );

		foreach ( $values_noescape as $value ) {
			$values[] = $dbcon->qstr( $value );
		}

		$sql .= join( ", ", $fields );
		$sql .= ") VALUES ( ";
		$sql .= join( ", ", $values );
		$sql .= " )";

	}

	$rs = $dbcon->Execute( $sql ) or
		die( "There was an error completing the request: " . $dbcon->ErrorMsg() );

	if ( $rs ) {

		// Run some default plugins. Plugins will not be run unless
		// they are pre-registered.
		//
		// These plugins should provide output to the user to reflect
		// their actions.

	} else {

		$udm->errorMessage( "There was an error processing the request." );

	}

	return true;

}

?>
