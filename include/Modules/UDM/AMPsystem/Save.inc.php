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
        $this->_save_plugins( );

    }

    function _save_plugins( ){
		$frmFieldValues = array_keys( $this->udm->_module_def );
        $plugin_options = array( );

		foreach ( $frmFieldValues as $field ) {
			if ( substr( $field, 0, 7 ) != "plugin_" ) continue;
            $option_def = split( '_', substr( $field, 7 ));
            $action = array_shift( $option_def );
            $namespace = array_shift( $option_def );
            $option_name = join( '_', $option_def );
            $option_value = $this->udm->form->getSubmitValue( $field );
            if ( is_array( $option_value )) {
                $option_value = join( ',', $option_value );
            }
            $plugin_options[$namespace][$action][$option_name] = $option_value;
        }

        $submitValues = $_POST;
        $new_plugins = array( );
        foreach( $submitValues as $submit_key => $value ){
			if ( substr( $submit_key, 0, 11 ) != "plugin_add_" ) continue;
            $new_plugins[] = substr( $submit_key, 11 );
        }
        /*
        foreach( $submitValues as $submit_key => $value ){
			if ( substr( $field, 0, 7 ) != "plugin_" ) continue;
            $option_def = split( '_', substr( $field, 7 ));
            $action = array_shift( $option_def );
            $namespace = array_shift( $option_def );
            $option_name = join( '_', $option_def );
            $option_value = $this->udm->form->getSubmitValue( $field );
            $plugin_options[$namespace][$action][$option_name] = $option_value;
        }*/

        $this->_add_plugins( $new_plugins );
        $this->_update_plugin_options( $plugin_options );

    }

    function _add_plugins( $plugins ){
        foreach( $plugins as $plugin_name ){
            $plugin_def = split( '_', $plugin_name);
            if ( !( $plugin = &$this->udm->registerPlugin( $plugin_def[0], $plugin_def[1]))) continue;
            $plugin->saveRegistration( $plugin_def[0], $plugin_def[1] );
        }
    }

    function _update_plugin_options( $plugin_options ) {
        foreach( $plugin_options as $namespace => $plugin_def ) {
            foreach( $plugin_def as $action => $options ){

                $plugin = &$this->udm->getPlugin( $namespace, $action );

                if ( is_array( $plugin )) {
                    $this->error( sprintf( AMP_TEXT_ERROR_USERDATA_PLUGIN_UPDATE_FAILED_MULTIPLE_INSTANCES, $namespace, $action ));
                    continue;
                }
                if ( !$plugin ) {
                    $this->error( sprintf( AMP_TEXT_ERROR_USERDATA_PLUGIN_UPDATE_FAILED_NOT_REGISTERED, $namespace, $action ));
                    continue;
                }

                $current_options = $plugin->getOptions( );
                $plugin_settings = array( 'plugin_id', 'plugin_active', 'plugin_priority');

                foreach( $options as $option_name => $option_value ){
                    if ( isset( $current_options[ $option_name ]) 
                         && $current_options[ $option_name ] == $option_value ) continue;
                    if ( array_search( $option_name, $plugin_settings ) !== FALSE ) continue;
                    if ( $current_options[ $option_name ] == null && $option_value == '' ) continue;
                    $plugin->saveOption( $option_name, $option_value );
                }
                if ( !$this->_update_plugin_settings( array_combine_key( $plugin_settings, $options ))) {
                    $this->error( sprintf( AMP_TEXT_ERROR_USERDATA_PLUGIN_UPDATE_FAILED, $namespace , $action ));
                }
            }
        }
    }

    function _update_plugin_settings( $settings ) {
        if ( !isset( $settings['plugin_id'])) return false;
        if ( !isset( $settings['plugin_active'])) $settings['plugin_active'] = false;
        if ( !isset( $settings['plugin_priority'])) $settings['plugin_active'] = 0;

        require_once( 'AMP/System/UserData/Plugin.php');
        $plugin = &new AMP_System_UserData_Plugin( $this->dbcon, $settings['plugin_id']);
        $plugin_data = array( 'active'   => $settings['plugin_active'],
                              'priority' => $settings['plugin_priority'] );
        $plugin->mergeData( $plugin_data );
        return $plugin->save( );

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

		$sql = "INSERT INTO userdata_fields (";
		$frmFieldValues = $udm->form->exportValues( array_keys( $udm->fields ) );

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
