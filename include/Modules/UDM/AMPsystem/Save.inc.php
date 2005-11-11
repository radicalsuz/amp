<?php

require_once( 'AMP/UserData/Plugin/Save.inc.php' );
require_once( 'AMP/System/UserData.php');

class UserDataPlugin_Save_AMPsystem extends UserDataPlugin_Save {

    var $name        = 'Save Form Structure';
    var $description = 'Saves the structure of the form.';

    var $available = false;

    function UserDataPlugin_Save_AMPsystem ( &$udm ) {
        $this->init( $udm );
    }

    function execute ( $options = null ) {

        // just make it work for now.
        udm_amp_save_admin( $this->udm, $options );

    }

}

function udm_amp_save_admin ( &$udm, $options = null ) {

	$dbcon = $udm->dbcon;

    #$udm->doPlugin( 'AMP', 'FixupDB' );

	// Insert or Update?
	if (isset( $udm->instance )) {

		$sql = "UPDATE userdata_fields SET ";

		$frmFieldValues = array_keys( $udm->_module_def );
        $skipFields = array();
		foreach ( $frmFieldValues as $field ) {
			if ( substr( $field, 0, 5 ) == "core_" ) $skipFields[] =  substr( $field, 5 );
		}

		foreach ( $frmFieldValues as $field ) {

			if ( array_search( $field, $skipFields ) ) continue;

			$sql_field = $field;

			if ( $field == 'id' ) continue;
			if ( substr( $field, 0, 5 ) == "core_" ) $sql_field = substr( $field, 5 );
            if ( strpos( $field, 'plugin_' ) === 0 ) continue;

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

        $fields[] = 'id';
        $values[] = lowerlimitInsertID( 'userdata_fields', 50 );

		$sql .= join( ", ", $fields );
		$sql .= ") VALUES ( ";
		$sql .= join( ", ", $values );
		$sql .= " )";

	}
	$rs = $dbcon->Execute( $sql ) or
		die( "There was an error completing the request: " . $dbcon->ErrorMsg() );

	if ( $rs ) {

        $udmDef = &new AMPSystem_UserData( $dbcon );
        $udmDef->clearItemCache( $udm->instance );

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
