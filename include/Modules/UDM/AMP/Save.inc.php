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
            ),
        'akismet_body_field' => array( 
                'type'  => 'text',
                'available' => true,
                'label' => 'Validate this field with akismet',
                'default' => ''
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
        $data['spam'] = $this->akismet_verify(  );

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

    function akismet_verify(  ) {
        if( !( $akismet = $this->to_akismet(  )  ) ) return false;
        return $akismet->isSpam(  );
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

    function &to_akismet( $item_data ) {
        $false = false;
        if( !AKISMET_KEY ) return $false;
        $options = $this->getOptions(  );
        if ( isset( $options['akismet_body_field'] ) && $options['akismet_body_field'] ) return $false;
        $all_data = $this->udm->getData(  );
        if( !isset( $all_data[$options['akismet_body_field']] ) ) return $false;

        $body_field = $all_data[$options['akismet_body_field']];

        $ak_data = array(  );
        $ak_data['author'] = $item_data['First_Name'] . ' ' . $item_data['Last_Name'];
        $ak_data['email'] = $item_data['Email'] ;
        $ak_data['type'] = 'form_input';
        $ak_data['website'] = $item_data['Website'];
        $ak_data['body'] = $item_data[ $body_field ];
        $ak_data['permalink'] = ( isset( $item_data['modin'] ) && $item_data['modin'] ) ? 
                                AMP_url_update( AMP_SITE_URL . '/' . AMP_CONTENT_URL_FORM, array( 'modin' => $item_data['modin'] ) ) : false;
        require_once( 'akismet/akismet.class.php' );
        $akismet = new Akismet( AMP_SITE_URL, AKISMET_KEY, $ak_data );

        if ( $akismet->isError( AKISMET_SERVER_NOT_FOUND ) ) {
            trigger_error( 'Akismet: Server Not Found' );
            return $false;
        }
        if ( $akismet->isError( AKISMET_RESPONSE_FAILED ) ) {
            trigger_error( 'Akismet: Response Failed' );
            return $false;
        }
        if ( $akismet->isError( AKISMET_INVALID_KEY ) ) {
            trigger_error( 'Akismet: Invalid Key' );
            return $false;
        }

        return $akismet;
    }
}

?>
