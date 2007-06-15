<?php

class AMP_Display_Form {

    var $_renderer;
    var $_fields;

    var $_order;
    var $_template;
    var $_blocks;

    var $_translations = array( );

    var $name;
    var $method = 'POST';
    var $action;

    var $_request_values_clean;
    var $_request_values;

    var $_rules;
    var $_rules_error_messages;
    var $_rules_errors;

    var $_messages['validation'] = array( 
        'required'  => '% is required' ,
        'email'     => '% must be a valid email address',
        'captcha'   => AMP_TEXT_ERROR_FORM_CAPTCHA_FAILED,
        'numeric'   => '% should be a number',
    );

    function AMP_Display_Form( ) {
        $this->__construct( );
    }

    function __construct( ) {
        $this->name = get_class( $this );
        $this->action = $_SERVER['PHP_SELF'];
        $this->_renderer = AMP_get_renderer( );
        $this->_after_init( );
    }

    function _after_init( ) {
        //interface
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
        foreach( $this->fields as $name => $field_def ) {
            if ( !isset( $field_def['type']) && $field_def['type'] ) continue;
            $render_method = $this->get_render_method( $name, $field_def['type'])
            if ( !method_exists( $this, $render_method )) {
                $output[ $name ]= $render_method( $name, $field_def, $this );
            }
            $output[$name] = $this->$render_method( $name, $field_def );
        }
    }

    function get_render_method( $type, $name ) {
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

    function render_field_default( $name, $field_def ) {

        return 
            $this->format_field( 
                $this->_renderer->label( $name, $field_def['label'] )
            . $this->format_element( 
                $this->_renderer->input( $name, $field_def['value'], $field_def['attr'] )
                ), $name );
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
        return $this->_renderer->div( $content, array( 'class' => 'element'));
    }

    function getValues( ) {
        if ( !$this->submitted( )) {
            $placed_values = array( );
            foreach( $this->fields as $field_name => $field_def ) {
                if ( !isset( $field_def['value'])) continue;
                $placed_values[$field_name] = $field_def['value'];
            }
            return $this->translate( $placed_values );
        }
        return $this->translate( $this->_request_values_clean );
    }

    function get( $field_name ) {
        if ( $this->submitted ) {
            return $this->_request_values_clean[$field_name];
        }
        return $this->fields[$field_name]['value'];
    }

    function set( $field_name, $value ) {
        $this->fields[$field_name]['value'] = $value;
    }

    function validate( ) {
        foreach( $this->_rules as $rule_key => $rule_def ) {
            $rule_method = $this->get_rule_method( $rule_def['name']);
            $value = $this->get( $rule_def['field'] );
            $result = call_user_func_array( $rule_method, $value )
            if ( !$result ) {
                $flash->add_error( 'There was a problem with some fields on this form' );
                $this->_rules_errors[$rule_def['field']] = $rule_def['name'];
                $this->_rules_error_messages[$rule_def['field']] = $rule_def['alert'];
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

}

?>
