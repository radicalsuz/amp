<?php

require_once( 'AMP/System/Form/XML.inc.php');

class AMP_System_Config_Form extends AMPSystem_Form_XML {

    var $_config_file;
    var $_config_text = array( );

    var $field_standard = array( 'type' => 'text' );
    var $field_numeric = array( 'type' => 'text', 'size' => '10' );
    var $field_header = array( 'type' => 'header' );
    var $field_boolean = array( 'type' => 'checkbox' );

    function AMP_System_Config_Form( $file ) {
        //$this->__construct( $file );
        $this->_config_file = $file;
        $name = get_class( $this );
        $this->init( $name, 'POST', $_SERVER['PHP_SELF'] );
    }

    function getFieldFile( ) {
        return $this->_config_file;
    }

    function _readXML( $file_name ) {
        $config_values = AMP_config_load( $file_name );
        $this->_config_text = array_merge( $this->_config_text, $this->parse_file_comments( $file_name ));
        return $this->make_fields( $config_values );
        
    }

    function parse_file_comments( $file_name ) {
        $custom_file_name = AMP_LOCAL_PATH . '/custom/' . $file_name . '.ini.php';
        if( !file_exists( $custom_file_name )) {
            $custom_file_name = 'Config/' . $file_name . '.ini.php';
            if( !file_exists_incpath( $custom_file_name )) return array( );
        }
        $fileRef = fopen( $custom_file_name, 'r');
        $result = array( );
        $current_header = '';
        while( $line = fgets( $fileRef)) {
            if( strlen( $line ) < 2 ) continue;
            if( substr( $line, 0, 1 ) == ';') {
                $current_header .= substr( $line, 1 ); 
                continue;
            }
            if( !$current_header) continue;

            $line_name = str_replace( array('[',']'), '', reset( split( '=', $line )));
            $result[ $line_name ] = $current_header;
            $current_header = '';

        }
        return $result; 
    }

    function make_fields( $values, $prefix = '' ) {
        $renderer = AMP_get_renderer( );
        $fields = array( );
        foreach( $values as $label => $value ) {
            $current_label = $prefix ? $prefix . '_' . $label : $label;
            $current_label = str_replace( ' ', '_', $current_label );
            
            if ( is_array( $value )) {
                $fields[$current_label] = $this->field_header;
                $fields[$current_label]['label'] = AMP_pluralize( ucwords( str_replace( '_', ' ', $current_label )));
                $sub_fields = $this->make_fields( $value, $current_label );
                $fields = $fields + $sub_fields;
                continue;
            }

            $base_field = $this->field_standard;
            if ( is_numeric( $value ) AND !is_bool( $value )) {
                $base_field = $this->field_numeric;
            }
            if ( is_bool( $value )) {
                $base_field = $this->field_boolean;
            }
            if ( isset( $this->_config_text[ $label ])) {
                $fields[$current_label.'__comment'] = array( 'type' => 'static', 'default' => $renderer->div( $this->_config_text[ $label ], array( 'class' => 'config_comment')) );
            }
           
            $fields[$current_label] = $base_field;
            $fields[$current_label]['label'] = ucwords( str_replace( '_', ' ', $label ) ) 
                                . $renderer->newline( )
                                . $renderer->span( 'amp_' . $current_label, array( 'class' => 'photocaption') );
            $fields[$current_label]['default'] = $value;
        }
        return $fields;

    }

}


?>
