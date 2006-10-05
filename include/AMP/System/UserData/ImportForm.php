<?php

class AMP_System_UserData_ImportForm extends AMPSystem_Form {

    var $_form_id = false;
    var $_import_filename = false;

    var $_fields_page_1 = array( 
            'filename' => array( 
                    'type' => 'file',
                    'label' => 'Upload a CSV file to import',
                    'required' => true,
                ),
            'action' => array( 
                    'type' => 'hidden',
                    'default' => 'import',
                ),
            'modin' => array( 
                    'type' => 'select',
                    'label' => 'Form to Import to',
                    'required' => true,
                    'lookup' => 'forms'
                )
        );

    var $_fields_page_2 = array( 
            'filename' => array( 
                    'type' => 'hidden',
                ),
                
            'modin' => array( 
                    'type' => 'hidden',
                ),
        );

    var $_fields_template = array( 
            'source_' => array( 
                    'type' => 'select',
                    'label' => 'Choose Source Field'
                    ),
            'target_' => array( 
                    'type' => 'select',
                    'label' => 'Choose Target Field'
                    ),
            'separator_' => array( 
                    'type' => 'html',
                    'default' => '<TR><td colspan=2><br /><HR width = "100%"/><BR /></td></tr>',
                    'label' => '<TR><td colspan=2><br /><HR width = "100%"/><br /></td></tr>',
                    //'label' => '<HR width = "100%"/><BR />'
                    )
        );

    var $_source_fields_lookup;
    var $_target_fields_lookup;

    var $_source_fields;

    var $_submit_group = 'submitImportAction';
    var $submit_button = array( 'submitImportAction' => array(
        'type' => 'group',
        'elements'=> array(
            'upload' => array(
                'type' => 'submit',
                'label' => 'Upload',
                ),
            'import' => array(
                'type' => 'submit',
                'label' => 'Import',
                ),
            'cancel' => array(
                'type' => 'submit',
                'label' => 'Cancel'),
            )
    ));

    function AMP_System_UserData_ImportForm( ){
        $this->__construct( );
    }

    function __construct( ){
        $name = 'AMP_System_UserData_ImportForm';
        $this->init( $name , 'POST', AMP_url_add_vars( AMP_SYSTEM_URL_FORMS, 'action=import'));
        $this->_init_request( );
        $this->_init_import_file( );
        $this->_init_fields( );
    }

    function _init_request( ) {
        if ( isset( $_REQUEST['modin']) && $_REQUEST['modin']) {
            $this->_form_id = $_REQUEST['modin'] ;
        }
        if ( $file_name = $this->_manageUpload( array( ), 'filename')) {
            $this->_import_filename = $file_name;
        } elseif ( isset( $_REQUEST['filename']) && $_REQUEST['filename'] ) {
            $this->_import_filename = $file_name;
            $this->_import_filename = $_REQUEST['filename'];
        }
    }

    function _init_upload_fields( ) {
        if ( $this->_form_id ) {
            $this->_fields_page_1['modin']['default'] = $this->_form_id;
        }
        $this->addFields( $this->_fields_page_1 );
        $this->removeSubmit( 'import');
        return;

    }

    function _load_target_field_defs( ){
        require_once( 'AMP/UserData.php');
        $udm = &new UserData( AMP_Registry::getDbcon( ), $this->_form_id, $admin = true );
        $fields_lookup = array( );
        $types_to_avoid = array ("html", "static", "header");

        foreach( $udm->fields as $field_name => $field_def ) {
            if ( !( isset( $field_def['enabled']) && $field_def['enabled'])) continue;
            if ( !isset( $field_def['type']) || ( array_search( $field_def['type'], $types_to_avoid ) !== FALSE )) {
                continue;
            }

            $label = $field_name;
            if ( isset( $field_def['label']) && $field_def['label']) {
                $label = $field_def['label'];
            }
            $fields_lookup[ $field_name ] = $label;
        }

        $fields_lookup['id'] = 'ID' ;
        require_once( 'AMP/System/User/Profile/Profile.php');
        $user = &new AMP_System_User_Profile( AMP_Registry::getDbcon( ));
        $db_fields   = $user->getAllowedKeys( );

        foreach( $fields_lookup as $field_key => $field_label ) {
            if ( !isset( $db_fields[ strtoupper( $field_key )] )) {
                unset( $fields_lookup[ $field_key ]);
            }
        }
        $this->_target_fields_lookup = $fields_lookup;

    }

    function _init_map_fields( ) {

        $this->removeSubmit( 'upload');

        $field_count = 0;
        $result_fields = $this->_fields_page_2;
        $result_fields['modin']['default'] = $this->_form_id;
        $result_fields['filename']['default'] = $this->_import_filename;

        $default_values = array_keys( $this->_target_fields_lookup );

        foreach( $this->_target_fields_lookup as $target_name => $target_label ) {
            foreach( $this->_fields_template as $template_key => $template_def ) {
                $lookup_var = '_' . $template_key . 'fields_lookup';
                if ( isset( $this->$lookup_var ) ) {
                    $template_def['values'] = $this->$lookup_var;
                    if ( $template_key == 'target_' ) {
                        $template_def['default'] = $default_values[ $field_count ];
                    }

                    $match = array_search( $target_label, $this->$lookup_var );
                    if ( $template_key == 'source_' && ( $match !== FALSE )) {
                       $template_def['default'] = $match; 
                    }
                }
                $result_fields[ $template_key . $field_count ] = $template_def;
            }
            ++$field_count;
            
        }

        $this->addFields( $result_fields );
    }

    function _init_fields( ) {
        if ( !( $this->_import_filename && $this->_form_id )) {
            return $this->_init_upload_fields( );
        }

        $this->_load_target_field_defs( );
        $this->_init_map_fields( );
    }

    function _init_import_file( ) {
        $this->_source_fields_lookup = $this->getSourceFields( );
        /*
        array( 
                'test' => 'Test Data Stuff Here',
                'or'   => 'Or Maybe Here'
            );
        */
    }

    function &_load_source( ){
        $empty_value = false;

        if ( !$this->_import_filename ){
            return $empty_value;
        }

        $full_path = AMP_LOCAL_PATH . AMP_CONTENT_URL_DOCUMENTS . $this->_import_filename;
        if ( !file_exists( $full_path )) {
            trigger_error( sprintf( AMP_TEXT_ERROR_FILE_EXISTS_NOT, $full_path ));
            return $empty_value;
        }
        $fp = @fopen( $full_path, 'rb' );

        if ( !$fp ) {
            return $empty_value;
        }
        return $fp;
    }

    function getSource( ) {
        $empty_value = false;

        $fp = &$this->_load_source( );
        if ( !$fp ) return $fp;

        //get the field definitions
        $this->_source_fields = fgetcsv( $fp, 32000 );

		// slurp in the data
		$MAXSIZE = 128000;
		
        $arr = array( );
		while ($cr_row = fgetcsv($fp,$MAXSIZE)) {
            $arr[] = $cr_row;
		}
			
		fclose($fp);
		if (!is_array($arr)) {
			trigger_error(  "Recordset had unexpected EOF (in serialized recordset)" );
			if (get_magic_quotes_runtime()) $err .= ". Magic Quotes Runtime should be disabled!";
			return $empty_value;
		}

        return $arr;
    }

    function getFormId( ) {
        return $this->_form_id;
    }

    function getSourceFields( ) {
        if ( isset( $this->_source_fields )) {
            return $this->_source_fields;
        }

        $fp = $this->_load_source( );
        if ( !$fp ) return $fp;

        //get the field definitions
        //$this->_source_fields = fgetcsv( $fp, 32000 );
        $source_fields_base = fgetcsv( $fp, 32000 );

        if ( !$source_fields_base ){
            $flash  = AMP_System_Flash::instance( );
            $flash->add_message( 'no fields found for ' . $this->_import_filename );
            return false;
        }

        foreach( $source_fields_base as $field_name ) {
            $this->_source_fields[ $field_name ] = $field_name;
        }
        fclose( $fp );
        return $this->_source_fields;
    }

    function getMap( ) {
        $raw_map = $this->getValues( );
        $final_map = array( );
        foreach( $raw_map as $field_name => $value ) {
            if ( substr( $field_name, 0, 7 ) != 'source_') continue;
            $field_count = substr( $field_name, 7 );
            $target_name = 'target_' . $field_count;
            if ( !$value 
                 || !isset( $raw_map[ $target_name ])
                 || !$raw_map[ $target_name ] 
                 || !isset( $this->_source_fields_lookup[$value])) {
                continue;

            }

            $final_map[ $this->_source_fields_lookup[$value] ] = $raw_map[ $target_name ];
        }
        return $final_map;
    }

}


?>
