<?php

function udm_amp_save ( &$udm, $options = null ) {

    if ( isset( $options['admin'] ) && $options['admin'] ) {

        $submitValues = $udm->form->exportValues();

        foreach ( $udm->fields as $fname => $fdef ) {
            if ( isset( $submitValues[ $fname ] ) )
                $frmFieldValues[ $fname ] = $submitValues[ $fname ];
        }

    } else {
        foreach ( $udm->fields as $fname => $fdef ) {
            if ( isset( $fdef['public'] ) && $fdef['public'] ) {
                $publicFields[] = $fname;
            }
        }

        $frmFieldValues = $udm->form->exportValues( $publicFields );
    }

    $dbcon = $udm->dbcon;
    // Insert or Update?
    if (isset( $udm->uid )) {

        $sql = "UPDATE userdata SET ";

        foreach ( $frmFieldValues as $field => $value ) {

            $elements[] = $field . "=" . $dbcon->qstr( $value );

        }

        $sql .= join( ", ", $elements );
        $sql .= " WHERE id=" . $dbcon->qstr( $udm->uid );

    } else {

        $sql = "INSERT INTO userdata (";

        $fields = array_keys( $frmFieldValues );
        $values_noescape = array_values( $frmFieldValues );

	foreach ( $values_noescape as $value ) {
		$values[] = $dbcon->qstr( $value );
	}

        $fields[] = "modin";
        $values[] = $udm->instance;

        $sql .= join( ", ", $fields );
        $sql .= ") VALUES (";
        $sql .= join( ", ", $values );
        $sql .= ")";

    }

    $rs = $dbcon->Execute( $sql ) or
        die( "There was an error completing the request: " . $dbcon->ErrorMsg() );

    if ( $rs ) {

        $udm->showForm = false;

        if ( !isset( $udm->uid ) ) {

            $udm->uid = $dbcon->Insert_ID();

        }

        // Run some default plugins. Plugins will not be run unless
        // they are pre-registered.
        //
        // These plugins should provide output to the user to reflect
        // their actions.

//      $udm->doAction( 'add_subscriber' );

//      $udm->doPlugin( 'AMP', 'auto_approve' );

//      $udm->doPlugin( 'AMP', 'email_admin' );
    	$udm->tryPlugin( 'AMP', 'email_user' );

    } else {

        $udm->errorMessage( "There was an error processing the request." );

    }

    return true;

}

?>
