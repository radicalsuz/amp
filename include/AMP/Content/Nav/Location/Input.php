<?php

require_once( 'AMP/Display/Form.php');

class AMP_Content_Nav_Location_Input extends AMP_Display_Form {

    var $xml_fields_source = 'AMP/Content/Nav/Location/Fields.xml';
    var $submit = 
            array( 'submitAction[save]' => array( 
                        'label' => 'Add This',
                        'attr' => array( 'onCLick' => 'return nav_location_add( this.form );'),
                        ));
    var $action = AMP_SYSTEM_URL_NAV_LOCATION;
    var $form_attr = array( 'onsubmit' => 'return nav_location_add( )');

    function AMP_Content_Nav_Location_Input ( ) {
        $this->__construct( );
    }

    function render_select_options( $name, $options = array( ) ) {
        if ( $name == 'position') return $options;
        return parent::render_select_options( $name, $options );
    }
    /*
    function render_label( $name, $text ) {
        return false;
    }

    function format_field( $content, $field_name ) {
        return $content;
    }

    function format_field_delimiter( ) {
        return false;
    }
    */

}



?>
