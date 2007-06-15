<?php

require_once( 'AMP/System/Form/XML.inc.php');

class AMP_System_Config_Form extends AMPSystem_Form_XML {

    var $_config_file;

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
        return $this->make_fields( $config_values );
        
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

            $fields[$current_label] = $base_field;
            $fields[$current_label]['label'] = ucwords( str_replace( '_', ' ', $label ) ) 
                                . $renderer->newline( )
                                . $renderer->span( $current_label, array( 'class' => 'photocaption') );
            $fields[$current_label]['default'] = $value;
            trigger_error( 'setting value of ' . $current_label . ' to ' . $value );
        }
        return $fields;

    }

}


?>
