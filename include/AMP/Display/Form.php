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
    var $form_attr = array( );

    var $_request_values_clean;
    var $_request_vars = array( );

    var $_rules = array( );
    var $_rules_error_messages = array( );
    var $_rules_errors = array( );
    var $isBuilt = true;

    var $_messages = array( 
        'validation' => array( 
            'required'  => '%s is required' ,
            'email'     => '%s must be a valid email address',
            'captcha'   => AMP_TEXT_ERROR_FORM_CAPTCHA_FAILED,
            'numeric'   => '%s should be a number',
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

    var $xml_fields_source;

    var $field_def_defaults = array( 
        'text' => array( 
            'attr' => array( 
                'size' => '40',
                ),
        ),
        'captcha' => array( 
            'attr' => array( 
                'size' => '10',
                ),
        ),
        'textarea' => array( 
            'attr' => array( 
                'rows'  => '4',
                'cols'  => '40'
                ),
        ),
    );

    var $_default_date_options = array(
        'language'         => 'en',
        'format'           => 'dMY',
        'minYear'          => 2003,
        'maxYear'          => 2012,
        'addEmptyOption'   => false,
        'emptyOptionValue' => '',
        'emptyOptionText'  => '&nbsp;',
        'optionIncrement'  => array('i' => 1, 's' => 1)
    );


    function AMP_Display_Form( ) {
        $this->__construct( );
    }

    function __construct( ) {
        if ( !$this->name ) $this->name = strtolower( get_class( $this ));
        if ( !$this->action ) $this->action = $_SERVER['PHP_SELF'];
        $this->_renderer = AMP_get_renderer( );
        $this->_init_request( );
        $this->_init_fields( );
        $this->_after_init( );
    }

    function _after_init( ) {
        //interface
    }

    function _after_add_fields( ) {
        $this->_init_default_values( );
        //interface
    }

    function _init_default_values( ) {
        if ( !empty( $this->_request_values_clean )) return;
        foreach( $this->_fields as $field_name => $def ) {
            $default = $this->get_field_def($field_name, 'default');
            $value = $this->get_field_def( $field_name, 'value') ;
            if ( $default && !$value ) {
                $this->set( $field_name, $default );
            }
        }

    }

    function _init_request( ) {
        if ( $this->method == 'POST') {
            $this->_request_vars = $_POST;
        } else {
            $this->_request_vars = $_GET;
        }
        if ( !empty( $this->_request_vars)) {
            $this->_request_values_clean = $this->clean( $this->_request_vars );
        }
        

    }

    function _init_fields( ) {
        $fields = $this->_init_fields_xml( );
        if ( !$fields ) return;
    }

    function _init_fields_xml( ) {
        if ( !$this->xml_fields_source ) return;
        return $this->read_xml_fields( $this->xml_fields_source );
    }

    function read_xml_fields( $xml_filename ) {
        $xml = file_get_contents( $xml_filename, 1 );
        return $this->add_xml_fields( $xml );

    }

    function add_xml_fields( $xml ) {
        $fields = $this->xml_to_fields( $xml );
        if ( !$fields ) return false;

        foreach( $fields as $name => $def ) {
            $this->add_field( $name, $def );
        }

        $this->_after_add_fields( );
        return count( $fields );

    }

    function xml_to_fields( $xml ) {

        require_once( 'XML/Unserializer.php');
        $parser = &new XML_Unserializer( );
        if ( !$xml ) return false;

        $status = $parser->unserialize( $xml );

        if ( PEAR::isError( $status )) {
            trigger_error( $status->getMessage( ));
            return false;
        }

        $results = $parser->getUnserializedData( );
        $fields = array( );
        foreach( $results as $key => $value ) {
            $fields[$key] = AMP_Display_Form::update_legacy_xml( $value );
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
            $output_type = ( $field_def['type'] == 'hidden') ? 'hidden' : 'visible';

            $render_method = $this->get_render_method( $name, $field_def['type']);
            if ( !method_exists( $this, $render_method )) {
                $output[ $output_type ][ $name ] = $render_method( $name, $field_def, $this );
            }
            $output[$output_type][$name] = $this->$render_method( $name, $field_def );
        }
        return    ( isset( $output['hidden']) ? join( "\n", $output['hidden']) : '' )
                . join( $this->format_field_delimiter( ), $output['visible']);
    }

    function format_field_delimiter( ) {
        return $this->_renderer->newline( );
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

    function render_field_captcha( $name, $field_def ) {
        return $this->format_field( 
                    $this->_renderer->div( '', array( 'class' => 'label'))
                    . $this->_renderer->div( 
                        $this->_renderer->image( AMP_url_add_vars( AMP_CONTENT_URL_CAPTCHA, array( 'key='. AMP_SYSTEM_UNIQUE_VISITOR_ID ) ), array( 'align' => 'center'))
                        , array( 'class' => 'element')), 
                   'captcha_image') 
                . $this->format_field_delimiter( )
                . $this->render_field_default( $name, $field_def );
    }

    function render_field_textarea( $name, $field_def ) {
        return 
            $this->format_field( 
              $this->render_label( $name, $this->get_field_def( $name, 'label') )
            . $this->format_element( 
                $this->_renderer->textarea( $name, $this->get( $name ), $this->get_field_def( $name, 'attr') )
                ), $name );
    }

    function render_field_select( $name, $field_def ) {
        $options = array( );
        if ( isset( $field_def['lookup']) && $field_def['lookup']) {
            $options = AMP_evalLookup( $field_def['lookup']);
        }
        if ( isset( $field_def['values']) && is_array( $field_def['values'])) {
            foreach( $field_def['values'] as $key => $value ) {
                if ( !is_array( $value ) ) {
                    $options[$value] = $value;
                    continue;
                }
                if ( isset( $value['key']) && isset( $value['value'])) {
                    $options[ $value['key']] = $value['value'];
                }
            }
        }
        $options = $this->render_select_options( $name, $options );

        return 
            $this->format_field( 
              $this->render_label( $name, $this->get_field_def( $name, 'label' ) )
            . $this->format_element( 
                $this->_renderer->select( $name, $this->get( $name ), $options, $this->get_field_def( $name, 'attr') )
                ), $name );
    }

    function render_label( $name, $text ) {
        if ( $this->get_field_def( $name, 'required')) {
            $text = $this->_renderer->span( '* ', array( 'class' => 'required', 'title' => 'required')) . $text;
        }
        return $this->_renderer->label( $name, $text );
    }

    function render_select_options( $name, $options = array( ) ) {
        $default_option_var = 'option_text_default_' . strtolower( $name );
        $default_option = ( isset( $this->$default_option_var )) ? $this->$default_option_var : AMP_TEXT_OPTION_DEFAULT;
        if ( !empty( $options ) && is_array( $options )) {
            return array( '' => $default_option ) + $options;
        }

        $blank_option_var = 'option_text_blank_' . strtolower( $name );
        $blank_option = ( isset( $this->$blank_option_var )) ? $this->$blank_option_var : AMP_TEXT_OPTION_BLANK;
        return array( '' => $blank_option );
    }

    function render_field_default( $name, $field_def ) {

        return 
            $this->format_field( 
              $this->render_label( $name, $this->get_field_def( $name, 'label') )
            . $this->format_element( 
                $this->_renderer->input( $name, $this->get( $name ), $this->get_field_def( $name, 'attr') )
                ), $name );
    }

    function render_field_header( $name, $field_def ) {
        $display = $this->get_field_def( $name, 'value' ) ;
        if ( !$display ) {
            $display = $this->get_field_def( $name, 'default' ) ;
        }
        if ( !$display ) {
            $display = $this->get_field_def( $name, 'label' ) ;
        }
        return $this->format_header( $display, $name );
    }

    function render_field_hidden( $name, $field_def ) {
        $attr = $this->get_field_def( $name, 'attr' );
        $attr['type'] = 'hidden';
        return $this->_renderer->input( $name, $this->get( $name ), $attr );
    }

    function render_field_static( $name, $field_def ) {
        $display = $this->get_field_def( $name, 'value' ) ;
        if ( !$display ) {
            $display = $this->get_field_def( $name, 'default' ) ;
        }
        if ( !$display ) {
            $display = $this->get_field_def( $name, 'label' ) ;
        }
        return $this->format_field( $display, $name );
    }

    function render_field_date( $name, $field_def ) {
        $date_options = $this->render_date_options( $field_def ) ;
        $date_element = '';
        $current_value = $this->get( $name );
        foreach( $date_options['selects'] as $date_segment => $values ) {
            if ( $date_options['addEmptyOption']){
                $values = array( '' => $date_options['emptyOptionText']) + $values;
            }
            $date_element .= $this->_renderer->select( $name . '['.$date_segment.']', $current_value[ $date_segment ], $values, $this->get_field_def( $name, 'attr')); 
        }

        return 
            $this->format_field( 
                  $this->render_label( $name, $this->get_field_def( $name, 'label' ))
                . $this->format_element( $date_element )
            , $name );
    }

    function render_date_options( $field_def ) {
        if ( !( isset( $field_def['options']) && is_array( $field_def['options']))) $field_def['options'] = array( );
        $date_options = array_merge( $this->_default_date_options, $field_def['options']);
        require_once( 'HTML/QuickForm/date.php');
        $date_renderer = new HTML_QuickForm_date( );
        $locale = $date_renderer->_locale[ $date_options['language']] ;
        for ($i = 0, $length = strlen($date_options['format']); $i < $length; $i++) {
            $sign = $date_options['format']{$i};
            $options = array( );
            switch ($sign) {
                case 'D':
                    // Sunday is 0 like with 'w' in date()
                    $options = $locale['weekdays_short'];
                    break;
                case 'l':
                    $options = $locale['weekdays_long'];
                    break;
                case 'd':
                    $options = $date_renderer->_createOptionList(1, 31);
                    break;
                case 'M':
                    $options = $locale['months_short'];
                    array_unshift($options , '');
                    unset($options[0]);
                    break;
                case 'm':
                    $options = $date_renderer->_createOptionList(1, 12);
                    break;
                case 'F':
                    $options = $locale['months_long'];
                    array_unshift($options , '');
                    unset($options[0]);
                    break;
                case 'Y':
                    $options = $date_renderer->_createOptionList(
                        $date_options['minYear'],
                        $date_options['maxYear'], 
                        $date_options['minYear'] > $date_options['maxYear']? -1: 1
                    );
                    break;
                case 'y':
                    $options = $date_renderer->_createOptionList(
                        $date_options['minYear'],
                        $date_options['maxYear'],
                        $date_options['minYear'] > $date_options['maxYear']? -1: 1
                    );
                    array_walk($options, create_function('&$v,$k','$v = substr($v,-2);')); 
                    break;
                case 'h':
                    $options = $date_renderer->_createOptionList(1, 12);
                    break;
                case 'H':
                    $options = $date_renderer->_createOptionList(0, 23);
                    break;
                case 'i':
                    $options = $date_renderer->_createOptionList(0, 59, $date_options['optionIncrement']['i']);
                    break;
                case 's':
                    $options = $date_renderer->_createOptionList(0, 59, $date_options['optionIncrement']['s']);
                    break;
                case 'a':
                    $options = array('am' => 'am', 'pm' => 'pm');
                    break;
                case 'A':
                    $options = array('AM' => 'AM', 'PM' => 'PM');
                    break;
                default:
                    break;
            }
            if ( empty( $options ) ) continue;
            $date_options['selects'][$sign] = $options;

        }
        return $date_options;

    }

    function render_submit( ) {
        $output = '';
        foreach( $this->submit as $name => $def ) {
            $attr = isset( $def['attr']) ? $def['attr'] : array( );
            $output .= $this->_renderer->submit( $name, $def['label'], $attr );
        }
        return $this->format_field( 
                $this->_renderer->div( '', array( 'class' => 'label'))
                . $this->_renderer->div( $output, array( 'class' => 'element submit')) 
                , 'submit');
        /*
        return $this->format_field( 
                $this->_renderer->div( $output, array( 'class' => 'submit')), 
                'submit'
                );
        */
    }

    function format_header( $content, $field_name ) {
        return $this->_renderer->div( $content, array( 'class' => 'form_header')) 
        ;
               // . $this->_renderer->div( false, array( 'class' => 'spacer')) 
        //        . $this->_renderer->newline( 1, array( 'clear' => 'all'));
    }

    function format_field( $content, $field_name ) {
        $item_class = 'row';
        if ( isset( $this->_rules_errors[$field_name] )) {
            $content .= $this->_renderer->newline( 2 )
                        . $this->_renderer->div( $this->_rules_error_messages[$field_name], array( 'class' => 'error'));
            $item_class = 'row row_error';
        }
        return $this->_renderer->div( $content, array( 'class' => $item_class ));
    }

    function format_element( $content ) {
        return $this->_renderer->div( $content, array( 'class' => 'element'));
    }

    function format_form( $content ) {
        $form_attr = array_merge( $this->form_attr, array( 'name' => $this->name, 'method' => $this->method, 'action' => $this->action ));
        return $this->_renderer->form( $content, $form_attr ); 
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

    function setValues( $values ) {
        foreach( $values as $field_name => $value ) {
            $this->set( $field_name, $value );
        }
    }

    function get( $field_name ) {
        if ( $this->submitted( ) ) {
            if ( !isset( $this->_request_values_clean[$field_name])) return false;
            return $this->_request_values_clean[$field_name];
        }
        return $this->get_field_def( $field_name, 'value' );
    }

    function set( $field_name, $value ) {
        $this->_fields[$field_name]['value'] = $value;
    }

    function validate( ) {
        $validation_okay = true;
        $flash = &AMP_System_Flash::instance( );

        foreach( $this->_rules as $rule_key => $rule_def ) {
            $rule_method = $this->get_rule_method( $rule_def['name']);
            $value = $this->get( $rule_def['field'] );
            $result = call_user_func_array( $rule_method, $value );
            if ( !$result ) {
                $flash->add_error( 'There was a problem with some fields on this form', 'form_errors' );
                $label = $this->get_field_def( $rule_def['field'], 'label' ) ;

                $this->_rules_errors[$rule_def['field']] = $rule_def['name'];
                $this->_rules_error_messages[$rule_def['field']] = sprintf( $rule_def['alert'], $label );

                $validation_okay = false;
            }
        }

        return $validation_okay;
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
        $this->_fields[$name] = $this->field_def_validate( $def );

        if ( isset( $def['rules'])) {
            foreach( $def['rules'] as $rule_def ) {
                $this->add_rule( $rule_def['type'], $name, $rule_def['message']);
            }
        }
        if ( isset( $def['required']) && $def['required']) {
            $this->add_rule( 'required', $name );
        }

        if ( $def['type'] == 'captcha') {
            $this->add_rule( 'captcha', $name );
        }
        $this->revise_order( $name, $order );
    }

    function update_legacy_xml( $def ) {
        $new_def = $def;
        if ( isset( $def['size'])) {
            if ( $def['type']=='textarea') {
                $sizes = split( ':', $def['size']);
                $new_def['attr']['rows'] = $sizes[0];
                $new_def['attr']['cols'] = $sizes[1];
            } else {
                $new_def['attr']['size'] = $def['size'];
            }

            unset( $new_def['size']);
        } 
        return $new_def;
    }

    function field_def_validate( $def ) {
        $new_def = AMP_Display_Form::update_legacy_xml( $def );
        if ( !isset( $this->field_def_defaults[ $def[ 'type']])) {
            return $new_def;
        }
       
        foreach( $this->field_def_defaults[ $def['type']] as $key => $default_value ) {
            if ( isset( $new_def[$key]) && !is_array( $new_def[$key])) continue;
            if ( isset( $new_def[$key]) && is_array( $new_def[$key]) && is_array( $default_value )) {
                foreach( $default_value as $segment_key => $segment_value ) {
                    if ( isset( $new_def[ $key ][$segment_key])) continue;
                    $new_def[ $key ][$segment_key]  = $segment_value;
                }
                continue;
            }
            $new_def[$key] = $default_value;
        }
        
        return $new_def;

    }

    function drop_field( $field_name ) {
        unset( $this->_fields[ $field_name ]);
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
            foreach( $this->submit as $key => $submit_def ) {
                if ( $this->assert_var( $key )) return $key;
            }
        }
        return false;
    }

    function assert_var( $var_name ) {
        if ( !isset( $this->_request_vars[ $var_name ])) return false;
        return $this->_request_vars[$var_name];
    }

    function clean( $values ) {
        return $this->clean_constants( $values );
    }

    function clean_constants( $values ) {
        foreach( $this->_fields as $name => $field_def ) {
            if ( isset( $field_def['constant']) && $field_def['constant']) {
                $values[$name] = $field_def[$name]['default'];
            }

        }
        return $values;
    }

    function getIdValue( ) {
        return $this->get( 'id');
    }

    function initNoId( ) {
        //legacy stub
    }
    function Build( ) {
        //legacy stub
    }
    function applyDefaults( ) {
        //legacy stub
    }

}

?>
