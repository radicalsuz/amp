<?php

require_once( 'AMP/UserData/Plugin/Save.inc.php');

class UserDataPlugin_Save_Related extends UserDataPlugin_Save {
    
    var $options = array( 
        'related_form_id' => array( 
            'type'      => 'select',
            'label'     => 'Related Form',
            'default'   => '',
            'available' => true
        ),
        'related_form_owner_field' => array( 
            'type'      => 'select',
            'label'     => 'Related Form Owner Field ( to store this Uid )',
            'default'   => '',
            'available' => true
        ),
        'badge_description' => array( 
            'type'      => 'select',
            'label'     => 'Badge to format added items',
            'default'   => '29',
            'available' => true
        ),
        'add_button_text' => array( 
            'type'      => 'text',
            'label'     => 'Text for add button',
            'default'   => 'Add This',
            'available' => true
        ),
        'included_fields' => array( 
            'type'      => 'textarea',
            'label'     => 'Fields to include',
            'default'   => '',
            'available' => true
        )


    );

    var $_field_prefix = 'related';
    var $_hidden_def = array( 
        'type' => 'hidden',
        'public' => true,
        'enabled' => true,
    );

    var $_included_fieldnames = array( );

    var $available = true;
    var $add_button = array( 
        'type' => 'button',
        'label' => 'Add This',
        'enabled' => true,
        'public' => true,
        'attr' => array( 'onClick' => 'related_add( this.form, Array( %1$s ), %3$s); related_describe( this.form, Array( %1$s), %2$s, %3$s, "%4$s_");' )
    );
    var $add_div = array( 
        'type' => 'static',
        'enabled' => true,
        'public' => true,
        'default' => '<div id="related_items_%s"></div>',
    );

    function UserDataPlugin_Save_Related( &$udm, $plugin_instance = null ) {
        $this->init( $udm, $plugin_instance );
    }
    
    function save( $data, $options = array( )) {
        $options = array_merge( $this->getOptions( ), $options );
        if ( !( isset( $options['related_form_id']) && $options['related_form_id'])) {
            trigger_error( sprintf( AMP_TEXT_ERROR_NOT_DEFINED, get_class( $this ), 'related_form_id'));
            return;
        }
        foreach( $this->_included_fieldnames as $fieldname ) {
            if ( $this->fields[$fieldname]['type'] == 'select') {
                $data[$fieldname] = $_POST[ $this->addPrefix( $fieldname )];
            }
        }

        $data_sets = $this->convert_to_sets( $data );
        foreach( $data_sets as $values ) {
            if ( isset( $options['related_form_owner_field']) && $options['related_form_owner_field']) {
                $values[ $options['related_form_owner_field']] = $this->udm->uid;
            }

            $udm = &new UserDataInput( $this->dbcon, $options['related_form_id'], $this->udm->admin );
            $udm->setData( $values );
            $results[] = $udm->saveUser( );
            unset( $udm );
        }
        return true;

    }

    function convert_to_sets( $data ) {
        $data_sets = array( );
        foreach( $data as $key => $value_set ) {
            $counter = 0;
            foreach( $value_set as $value ) {
                $data_sets[$counter][$key] = $value;
                ++$counter;
            }
            
        }
        return $data_sets;
    }
    
    function _register_fields_dynamic( ) {
        $header = &AMP_get_header( );
        $header->addJavascript( 'scripts/ajax/prototype.js', 'prototype');
        $header->addJavascript( 'scripts/related.js', 'related_form');

        $options = $this->getOptions( );
        if ( !( isset( $options['related_form_id']) && $options['related_form_id'])) {
            trigger_error( sprintf( AMP_TEXT_ERROR_NOT_DEFINED, get_class( $this ), 'related_form_id'));
            return;
        }

        $related_udm = &new UserData( $this->dbcon, $options['related_form_id'], $this->udm->admin );
        $this->add_div['default'] = sprintf( $this->add_div['default'], $options['related_form_id'] );
        $this->fields['add_div'] = $this->add_div;

        $this->_included_fieldnames = 
                ( isset( $options['included_fields'] ) && $options['included_fields']) 
                    ?  preg_split( "/\s?,\s?/", $options['included_fields'] ) 
                    : array_keys( $related_udm->fields );
        $included_fields = array_combine_key( $this->_included_fieldnames, $related_udm->fields );

        $this->fields = array_merge( $this->fields, $included_fields);

        $dom_field_names = $this->convertFieldNamestoUDM( $related_udm->fields, $keys = true );
        $add_button_targets = join( "', '", $dom_field_names );
        $this->add_button['attr']['onClick'] = sprintf( $this->add_button['attr']['onClick'], '\''.$add_button_targets.'\'', $options['badge_description'], $options['related_form_id'], $this->_field_prefix);
        $this->fields['add_button'] = $this->add_button;

        foreach( $dom_field_names as $dom_field ) {
            if ( isset( $_POST[ $dom_field ]) && is_array( $_POST[ $dom_field ])) {
                foreach( $_POST[ $dom_field ] as $key => $value ) {
                    $this->fields[str_replace( $this->_field_prefix .'_', '', $dom_field ).'['.$key.']'] = $this->_hidden_def;
                }
            }
        }
        if ( isset( $this->udm->form )) {
            $this->udm->form->setAttribute( 'onSubmit', 'related_add( this, Array( \''.$add_button_targets.'\'));' ); 
        }

    }

    function _register_options_dynamic( ) {
        if ( !$this->udm->admin ) return;
        $form_set = AMP_lookup( 'forms');
        $this->options['related_form_id']['values']    = array( '' => AMP_TEXT_OPTION_DEFAULT ) + $form_set;

        $badge_set= AMP_lookup( 'badges');
        $this->options['badge_description']['values']    = array( '' => AMP_TEXT_OPTION_DEFAULT ) + $badge_set;

        $options = $this->getOptions( );
        if ( !$options['related_form_id']) return;
        require_once( 'AMP/UserData/Lookups.inc.php');

        $field_set = AMPSystem_Lookup::instance('formFields', $options['related_form_id']);

        if ( !$field_set ) $field_set = array( );
        $this->options['related_form_owner_field']['values']    = array( '' => AMP_TEXT_OPTION_DEFAULT ) + $field_set;
        

    }

    function getSaveFields( ) {
        return $this->_included_fieldnames;
        /*
        $values = $this->getAllDataFields( );
        $return_values = array( );
        foreach( $values as $field_name ) {
            $return_values[] = $field_name . '[]';
        }
        return $return_values;
        */
    }

}


?>
