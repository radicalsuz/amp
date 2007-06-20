<?php

class AMP_Display_Form {

    var $_renderer;

    var $_order = array( );
    var $_template;
    var $_blocks;

    var $_translations = array( );

    var $name;
    var $method = 'POST';
    var $action;

    var $_request_values_clean;
    var $_request_vars;

    var $_rules;
    var $_rules_error_messages;
    var $_rules_errors;

    var $_messages = array( 
        'validation' => array( 
            'required'  => '% is required' ,
            'email'     => '% must be a valid email address',
            'captcha'   => AMP_TEXT_ERROR_FORM_CAPTCHA_FAILED,
            'numeric'   => '% should be a number',
        )
    );

    var $_fields = array( );
        //label
        //type
        //attr
        //value
        //block
        //lookup
        //default

    var $submit = array( 'save' => array( 'label' => 'Save Changes' ));


    function AMP_Display_Form( ) {
        $this->__construct( );
    }

    function __construct( ) {
        $this->name = get_class( $this );
        $this->action = $_SERVER['PHP_SELF'];
        $this->_renderer = AMP_get_renderer( );
        $this->_init_request( );
        $this->_init_fields( );
        $this->_after_init( );
    }

    function _after_init( ) {
        //interface
    }

    function _init_request( ) {
        if ( $this->method == 'POST') {
            $this->_request_vars = $_POST;
        } else {
            $this->_request_vars = $_GET;
        }

    }

    function _init_fields( ) {
        $fields = $this->_init_fields_xml( );
        if ( !$fields ) return;
        foreach( $fields as $name => $def ) {
            $this->add_field( $name, $def );
        }
    }

    function _init_fields_xml( ) {
        $xml_file = 'Modules/Share/Public/Fields.xml';
        require_once( 'XML/Unserializer.php');
        $parser = &new XML_Unserializer( );
        $xml = file_get_contents( $xml_file, 1 );
        if ( !$xml ) return false;
        $status = $parser->unserialize( $xml );

        if ( PEAR::isError( $status )) {
            trigger_error( $status->getMessage( ));
            return false;
        }

        $results = $parser->getUnserializedData( );
        $fields = array( );
        foreach( $results as $key => $value ) {
            $fields[$key] = $this->update_legacy_xml( $value );
        }
        return $fields;

    }

    function execute( ) {
        return $this->render_form( );
    }

    function render_form( ) {
        return
              $this->render_header( )
              . $this->format_form( 
                    $this->render_fields( )
                    . $this->render_submit( )
                )
            . $this->render_footer( );
    }

    function render_header( ) {
        return false;
    }

    function render_footer( ) {
        return false;
    }

    function render_fields( ) {
        $output = array( );
        foreach( $this->_fields as $name => $field_def ) {
            if ( !isset( $field_def['type']) && $field_def['type'] ) continue;
            $render_method = $this->get_render_method( $name, $field_def['type']);
            if ( !method_exists( $this, $render_method )) {
                $output[ $name ]= $render_method( $name, $field_def, $this );
            }
            $output[$name] = $this->$render_method( $name, $field_def );
        }
        return join( $this->_renderer->newline( ), $output );
    }

    function get_render_method( $name, $type ) {
        //change the rendering for all examples of a field
        $custom_method_constant = 'AMP_RENDER_FORM_FIELD_' . strtoupper( $type ) ;
        if ( defined( $custom_method_constant )) return constant( $custom_method_constant );

        //target a specific field
        $custom_method_constant2 = 'AMP_RENDER_' . strtoupper( $this->name . '_FIELD_' . $name );
        if ( defined( $custom_method_constant2 )) return constant( $custom_method_constant2 );

        $local_method = 'render_field_' . $type;
        if ( method_exists( $this, $local_method )) {
            return $local_method;
        }
        return 'render_field_default';
    }

    function render_field_textarea( $name, $field_def ) {
        return 
            $this->format_field( 
                $this->_renderer->label( $name, $this->get_field_def( $name, 'label' ) )
            . $this->format_element( 
                $this->_renderer->textarea( $name, $this->get( $name ), $this->get_field_def( $name, 'attr') )
                ), $name );
    }

    function render_field_default( $name, $field_def ) {

        return 
            $this->format_field( 
                $this->_renderer->label( $name, $this->get_field_def( $name, 'label' ) )
            . $this->format_element( 
                $this->_renderer->input( $name, $this->get( $name ), $this->get_field_def( $name, 'attr') )
                ), $name );
    }

    function render_field_header( $name, $field_def ) {
        return $this->format_header( $this->get_field_def( $name, 'label'), $name );
    }

    function render_submit( ) {
        $output = '';
        foreach( $this->submit as $name => $def ) {
            $attr = isset( $def['attr']) ? $def['attr'] : array( );
            $output .= $this->_renderer->submit( $name, $def['label'], $attr );
        }
        return $this->_renderer->div( $output, array( 'class' => 'submit'));

    }

    function format_header( $content, $field_name ) {
        return $this->_renderer->div( $content, array( 'class' => 'form_header')) . $this->_renderer->div( false, array( 'class' => 'spacer'));
    }

    function format_field( $content, $field_name ) {
        $item_class = 'row';
        if ( isset( $this->_rules_errors[$field_name] )) {
            $content = $this->_rules_error_messages[$field_name] . $content;
            $item_class = 'row row_error';
        }
        return $this->_renderer->div( $content, array( 'class' => $item_class ));
    }

    function format_element( $content ) {
        return $this->_renderer->div( $content, array( 'class' => 'element'));
    }

    function format_form( $content ) {
        return $this->_renderer->form( $content, array( 'name' => $this->name, 'method' => $this->method, 'action' => $this->action ));
    }

    function getValues( ) {
        if ( !$this->submitted( )) {
            $placed_values = array( );
            foreach( $this->_fields as $field_name => $field_def ) {
                if ( !isset( $field_def['value'])) continue;
                $placed_values[$field_name] = $field_def['value'];
            }
            return $this->translate( $placed_values );
        }
        return $this->translate( $this->_request_values_clean );
    }

    function get( $field_name ) {
        if ( $this->submitted( ) ) {
            return $this->_request_values_clean[$field_name];
        }
        return $this->get_field_def( $field_name, 'value' );
    }

    function set( $field_name, $value ) {
        $this->_fields[$field_name]['value'] = $value;
    }

    function validate( ) {
        foreach( $this->_rules as $rule_key => $rule_def ) {
            $rule_method = $this->get_rule_method( $rule_def['name']);
            $value = $this->get( $rule_def['field'] );
            $result = call_user_func_array( $rule_method, $value );
            if ( !$result ) {
                $flash->add_error( 'There was a problem with some fields on this form' );
                $this->_rules_errors[$rule_def['field']] = $rule_def['name'];
                $label = $this->get_field_def( $rule_def['field'], 'label' ) ;
                $this->_rules_error_messages[$rule_def['field']] = sprintf( $rule_def['alert'], $label );
            }

        }
    }

    function get_rule_method( $rule ) {
        if ( is_array( $rule )) {
            return $rule;
        }
        if ( method_exists( $this, 'validate_' . $rule )) {
            return array( $this, 'validate_' . $rule );
        }
        if ( function_exists( $rule )) {
            return $rule;
        }
        return false;

    }

    function translate( $values ) {
        return $values;
    }

    function add_rule( $rule_name, $field_name, $message = false ) {
        if ( !$message && isset( $this->_messages['validation'][$rule_name])) {
            $message = $this->_messages['validation'][$rule_name];
        }

        $this->_rules[] = array(
            'field' => $field_name,
            'name'  => $rule_name,
            'alert' => $message,
        );  

    }

    function validate_required( $value ) {
        return ( !empty( $value ));
    }

    function validate_email( $value ) {
        $regex = '/^((\"[^\"\f\n\r\t\v\b]+\")|([\w\!\#\$\%\&\'\*\+\-\~\/\^\`\|\{\}]+(\.[\w\!\#\$\%\&\'\*\+\-\~\/\^\`\|\{\}]+)*))@((\[(((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9])))\])|(((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9])))|((([A-Za-z0-9\-])+\.)+[A-Za-z\-]+))$/';
        if (preg_match($regex, $value)) {
            return true;
        }
        return false;

    }

    function validate_numeric($value ) {
        return is_numeric( $value );
    }

    function validate_captcha( $value ) {
        require_once( 'AMP/Form/Element/Captcha.inc.php');
        $captcha = &new PhpCaptcha( array( ) );
        return $captcha->Validate($value);
    }

    function add_field( $name, $def = array( 'type' => 'text' ), $order = 0 ) {
        $this->_fields[$name] = $def;
        $this->revise_order( $name, $order );

    }

    function update_legacy_xml( $def ) {
        if ( isset( $def['size'])) {
            if ( $def['type']=='textarea') {
                $sizes = split( ':', $def['size']);
                $def['attr']['rows'] = $sizes[0];
                $def['attr']['cols'] = $sizes[1];
            } else {
                $def['attr']['size'] = $def['size'];
            }
            unset( $def['size']);
        } else {
            $def['attr']['size'] = 40;
        }
        return $def;
    }

    function revise_order( $field_name, $order=0 ) {
        $new_order = $order;
        if ( is_string( $order ) && $order ) {
            $new_order = $this->get_order_index( $order );
        }
        if ( !$new_order ) {
            $new_order=count( $this->_order );
        }

        foreach( $this->_order as $existing_field_name => $existing_order_value ) {
            if( $existing_order_value < $new_order ) continue; 
            $this->_order[ $existing_field_name ] = $existing_order_value + 1;
        }
        $this->_order[$field_name] = $new_order;

    }

    function get_order_index( $value ) {
        if ( isset( $this->_order[$value])) return $this->_order[$value];
        if ( strpos( $value, 'after_') === 0 ) {
            return $this->get_order_index( substr( $value, 6 )) + 1;
        }
        if ( strpos( $value, 'before_') === 0 ) {
            return $this->get_order_index( substr( $value, 7 )) - 1;
        }
        return false;
    }

    function get_field_def( $field_name, $attribute=false ) {
        if ( !isset( $this->_fields[$field_name])) return false;
        if ( !$attribute ) return $this->_fields[$field_name];
        if ( !isset( $this->_fields[$field_name][$attribute])) return false;
        return $this->_fields[$field_name][$attribute];
    }

    function submitted( ) {
        if ( is_array( $this->submit )) {
            return $this->assert_var( key( $this->submit ));
        }
    }

    function assert_var( $var_name ) {
        if ( !isset( $this->_request_vars[ $var_name ])) return false;
        return $this->_request_vars[$var_name];
    }

}

?>
