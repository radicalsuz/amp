<?php

require_once( 'AMP/UserData/Plugin.inc.php' );

class UserDataPlugin_CopyAdmin_AMPsystem extends UserDataPlugin {

    var $name = "Copy a plugin.";
    var $available = false;

    function UserDataPlugin_CopyAdmin_AMPsystem ( &$udm ) {
        $this->init( $udm );
    }

    function execute ( $options = null ) {

        // nasty hack, sigh.
        return udm_amp_copy_admin( $this->udm, $options );

    }
}


function udm_amp_copy_admin ( $udm, $options = null ) {

	$dbcon = $udm->dbcon;
	$old_modin=$udm->instance;
	#global $mod_id;

		$sql = "INSERT INTO userdata_fields (";
		$frmFieldValues = array_keys( $udm->_module_def );
		foreach ( $frmFieldValues as $field ) {
			if ( substr( $field, 0, 5 ) == "core_" ) $skipFields[] =  substr( $field, 5 );
            if ( substr( $field, 0, 7 ) == "plugin_" ) continue;
		}

		foreach ( $frmFieldValues as $field ) {

            if ( substr( $field, 0, 7 ) == "plugin_" ) continue;
			if ( array_search( $field, $skipFields ) ) continue;

			$sql_field = $field;
			if ( $field == 'redirect' ) continue;
            if ( $field == 'name') continue;

            // ugly, vicious hack.
			if ( $field == 'id' ) $sql_value = autoinc_check( userdata_fields, 50 );
			if ( $field=='core_name') { $sql_value = $dbcon->qstr( $udm->form->getSubmitValue( $field ) ); }
			if ( substr( $field, 0, 5 ) == "core_" ) $sql_field = substr( $field, 5 );

			if (!$sql_value) $sql_value = $dbcon->qstr( $udm->_module_def[$field] );

			$elements[] = $sql_field; 
			$values[] = $sql_value;
			
		}

		$sql .= join( ", ", $elements );
		$sql .= ") VALUES ( ";
		$sql .= join( ", ", $values );
		$sql .= " )";


	

	$rs = $dbcon->Execute( $sql ) or
		die( "There was an error completing the request: " . $dbcon->ErrorMsg() );


	if ( $rs ) {
		$rs2=$dbcon->GetArray("SELECT LAST_INSERT_ID()");
		$new_modin= join(",",$rs2[0]);
		
		$old_input_mod=$udm->form->getSubmitValue( 'core_modidinput' );
		$old_response_mod=$udm->form->getSubmitValue( 'core_modidresponse' );
		$options=array('old_modin'=>$old_modin, 'new_modin'=>$new_modin, 'old_name'=>$udm->name, 'new_name'=>$new_name, 'modtexts'=>array($old_input_mod, $old_response_mod));
		/*See current options for debug purposes
		foreach ($options as $key=>$kvalue){
			if (is_array($kvalue)) {
				foreach ($kvalue as $ykey=>$yvalue) {
					print $key.":  $ykey:   ".$yvalue."<BR>";
				}
			} else {
				print $key.":  ".$kvalue."<BR>";
			}
		}*/
		
		$options['new_module_id']=$udm->doPlugin('AMP', 'CopyModule', $options);
		$options=$udm->doPlugin('AMP', 'CopyModuletext', $options);

		
	
	} else {

		$udm->errorMessage( "There was an error processing the request." );

	}

	return $new_modin;

}

?>
