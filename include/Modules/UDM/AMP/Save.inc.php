<?php

require_once( 'AMP/UserData/Plugin/Save.inc.php' );

class UserDataPlugin_Save_AMP extends UserDataPlugin_Save {

    var $name = 'Save Submitted Data';
    var $description = 'Save the submitted data into the AMP database';

    var $available = true;
    var $options = array( 
        'captcha_verification' => array( 
                'type'  => 'checkbox',
                'available' => true,
                'label' => 'Use Captcha on public form',
                'default' => 0
            )
        );

    function UserDataPlugin_Save_AMP ( &$udm, $plugin_instance=null ) {
        $this->init( $udm, $plugin_instance );
    }

    function _register_fields_dynamic( ) {
        $options = $this->getOptions( );
        if ( ( isset( $options['captcha_verification']) &&  $options['captcha_verification'] )
             || ( !isset( $options['captcha_verification']))) {
            $this->verify_captcha( );
        }
    }

    function getSaveFields () {

        // The AMP save function only saves fields for which there is a
        // corresponding column in the userdata table
        //
        // note that only valid, accessible fields will actually be
        // returned. If the module is set to allow admin access, then all
        // "enabled" fields will be returned for saving. This decision process
        // is in the UserData object itself.

        //$db_fields   = $this->udm->dbcon->MetaColumnNames('userdata');
        $db_fields   = $this->_getColumnNames( 'userdata');
        $qf_fields   = $this->_include_file_fields( array_keys( $this->udm->form->exportValues() ));
        

        $save_fields = array_intersect( $db_fields, $qf_fields );
        $this->_field_prefix="";

        return $save_fields;

    }

    function _include_file_fields( $qf_fields ) {
        if ( !in_array( 'MAX_FILE_SIZE', $qf_fields )) {
            return $qf_fields;
        }

        foreach( $qf_fields as $fieldname ) {
            $value_point = strlen( $fieldname ) - 6;
            if ( substr( $fieldname, $value_point ) == '_value') {
                $qf_fields[] = substr( $fieldname, 0, $value_point )   ;
            }
        }
        return $qf_fields;

    }

    function save ( $data ) {

        $sql = ($this->udm->uid) ? $this->updateSQL( $data ) :
                                   $this->insertSQL( $data );

        $rs = $this->dbcon->Execute( $sql );

        if ( !$rs ) {
            trigger_error( "Unable to save request data using SQL $sql: " . $this->dbcon->ErrorMsg() );
            return false;
        }

        if ($rs) {
            if (!$this->udm->uid) $this->udm->uid = $this->dbcon->Insert_ID();
            return true;
        }

        return false;
    }

    function updateSQL ( $data ) {

        $dbcon =& $this->dbcon;

        $sql = "UPDATE userdata SET ";

        $save_fields = $this->getSaveFields();

        foreach ($save_fields as $field) {
            if ( !isset( $data[$field])) continue;
            $elements[] = $field . "=" . $dbcon->qstr( $data[$field] );
        }

        $sql .= implode( ", ", $elements );
        $sql .= " WHERE id=" . $dbcon->qstr( $this->udm->uid );

        return $sql;

    }

    function insertSQL ( $data ) {

        $dbcon =& $this->dbcon;

        $data['modin'] = $this->udm->instance;
        $data['created_timestamp'] = date( 'YmdHis') ;

        $fields = $this->getSaveFields();
        $fields[] = 'created_timestamp';

        $values_noescape = array_values( $data );

        foreach ( $fields as $field ) {
            $value = $data[$field];
            $values[] = $dbcon->qstr( $value );
        }

        $sql  = "INSERT INTO userdata (";
        $sql .= join( ", ", $fields ) .
                ") VALUES (" .
                join( ", ", $values ) .
                ")";

        return $sql;

    }

    function _getColumnNames( $sourceDef ) {
        if ( function_exists( 'AMP_get_column_names' ) ) {
            return AMP_get_column_names( $sourceDef );
        }
        trigger_error( 'BAD version of utility.functions.inc.php');
        return array( );
/*
        $reg = &AMP_Registry::instance();
        $definedSources = &$reg->getEntry( AMP_REGISTRY_SYSTEM_DATASOURCE_DEFS );
        if ( !$definedSources ) {
            $definedSources = AMP_cache_get( AMP_REGISTRY_SYSTEM_DATASOURCE_DEFS );
        }
        if ($definedSources && isset($definedSources[ $sourceDef ])) return $definedSources[ $sourceDef ];

        if ( !isset( $this->dbcon )) trigger_error( sprintf( AMP_TEXT_ERROR_NOT_DEFINED, get_class( $this ), 'dbcon' ));
        $colNames = $this->dbcon->MetaColumnNames( $sourceDef );
        $definedSources[ $sourceDef ] = $colNames;
        $reg->setEntry( AMP_REGISTRY_SYSTEM_DATASOURCE_DEFS, $definedSources );
        AMP_cache_set( AMP_REGISTRY_SYSTEM_DATASOURCE_DEFS, $definedSources );

        return $colNames;
        */
    }
}

?>
