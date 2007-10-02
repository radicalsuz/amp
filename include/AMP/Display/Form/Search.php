<?php
require_once( 'AMP/Display/Form.php');

class AMP_Display_Form_Search extends AMP_Display_Form {

    var $submit = array(
        'search' => array(
            'label'=> 'Search',
            )
        );
            //'type' => 'submit',
            //'public' => true,
            //'attr' => array( 'class' => 'searchform_element' ),
            //'separator' => 'endform',
            //'default'=>'search'

    var $method = 'GET';
    var $form_attr = array( 'class' => 'search_form');

    function AMP_Display_Form_Search( ) {
        $this->__construct( );
    }

    function field_def_validate( $def ) {
        $label = ( isset( $def['label']) && $def['label'] ) ? $def['label'] : false;
        if ( $label && !(( substr( $label, -1 ) == ':')) || isset( $def['default'])) {
           if ( array_search( $def['type'], array( 'select', 'multiselect')) === FALSE ) {
               unset( $def['label'] );
           }
        }
        return parent::field_def_validate( $def );
    }

    function add_field( $name, $def = array( ), $order = 0 ) {
        $label = ( isset( $def['label']) && $def['label'] ) ? $def['label'] : false;
        if ( $label && !( substr( $label, -1 ) == ':')) {
            unset( $def['label']);
           if ( $def['type'] == 'text' ) {
               $def['default'] = $label;
               if (!isset($def['attr']['onclick'])) $def['attr']['onclick'] = 'if (this.value==\''.$label.'\') this.value=\'\';' ; 
               if (!isset($def['attr']['onblur'])) $def['attr']['onblur'] = 'if (this.value==\'\') this.value=\''.$label.'\';' ; 
           }
        }
        parent::add_field( $name, $def, $order );
        if ( array_search( $def['type'], array( 'select', 'multiselect')) !== FALSE ) {
            $default_text= 'option_text_default_' . $name;
            $this->$default_text = $label;
        }
    }

    function clean( $values ) {
        $search_values = array( );
        foreach ($values as $ukey => $uvalue) {
            if (!isset( $this->_fields[ $ukey ] )) continue;
            if ( $uvalue==="" ) continue;
            if ( ($this->_fields[$ukey]['type']=='text') && ($uvalue == $this->_fields[$ukey]['label'])) continue;
            if ( ($this->_fields[$ukey]['type']=='date') && is_array( $uvalue )
                  && isset( $uvalue['M']) && isset( $uvalue['d']) && isset( $uvalue['Y'])
                  && $uvalue['M'] && $uvalue['d'] && $uvalue['Y'] ) {
                $search_values[ $ukey ] =  mktime(0,0,0, $uvalue['M'], $uvalue['d'], $uvalue['Y']);
                continue;
            }
            
            $search_values[ $ukey ] = $uvalue;
        }

        return $this->clean_constants( $search_values );
    }

    function getSearchValues( ) {
        return $this->getValues( );
    }

    function format_field( $content, $field_name ) {
        $separator_html = '';
        $separator = $this->get_field_def( $field_name, 'separator');
        if ( $separator && ( $sep_method = 'render_' . $separator ) && method_exists( $this, $sep_method )) {
            $separator_html = $this->$sep_method( $field_name );
        }
        return $separator_html . $this->_renderer->div( $content, array( 'class' => 'item'));
    }

    function render_newrow( $field_name ) {
        return $this->_renderer->invert_tag( 'div', '', array( 'class' => 'row'));
    }

    function format_form( $content ) {
        $formatted_content =
            $this->_renderer->div( 
                $this->_renderer->div( 
                    $content, array( 'class' => 'row'))
                    , array( 'class' => 'searchform')
                );
        $form_attr = array_merge( $this->form_attr, array( 'name' => $this->name, 'method' => $this->method, 'action' => $this->action ));
        return $this->_renderer->form( $formatted_content, $form_attr ); 
    }

    function format_field_delimiter( ) {
        return '';
    }
}

?>
