<?php

require_once( 'AMP/UserData/Plugin.inc.php');
require_once( 'AMP/Form/XML.inc.php');

class UserDataPlugin_Override_QuickForm extends UserDataPlugin {
	var $options = array (  
        'button_label'=>  array(
            'type'=>'text',
            'available'=>true,
            'label'=>'Label for Submit Button',
            'default'=>'Submit' ),
        'override_file' => array( 
            'type' => 'select',
            'available' => true,
            'label' => 'Override Template',
            'default' => ''
        )
		);

    var $_formEngine;
    var $available = true;  

    function UserDataPlugin_Override_QuickForm( &$udm, $plugin_instance = null ) {
        $this->init( $udm, $plugin_instance );
    }

    function _register_fields_dynamic( ) {
        $options = $this->getOptions( );
        if ( !( isset( $options['override_file']) && $options['override_file'])) return;
        if ( !file_exists_incpath( $options['override_file'])) {
            trigger_error( "can't find override file {$options['override_file']}");
            return;
        }
        $override_fields = $this->_read_xml_fields( $options['override_file']);
        if ( !$override_fields ) {
            trigger_error( "XML read failed for override file {$options['override_file']}");
            return;
        }
        $this->udm->fields = array_merge( $this->udm->fields, $override_fields );

    }

    function _register_options_dynamic( ) {
        if ( !$this->udm->admin ) return;
        if(  $override_set = AMPfile_list( 'custom', 'xml') ) {
            $this->options['override_file']['values']    = array( '' => AMP_TEXT_OPTION_DEFAULT ) + $override_set;
        } else {
            $this->options['override_file']['values']    = array( '' => AMP_TEXT_OPTION_BLANK );
        }
    }

    function _read_xml_fields( $file_name ) {
        $fieldsource = & new AMPSystem_XMLEngine( $file_name );

        if ( $fields = $fieldsource->readData() ) {
            foreach( $fields as $field_name => $field_def ) {
                if( !isset( $fields[$field_name]['enabled'])) {
                    $fields[$field_name]['enabled'] = true;
                }
                if( !isset( $fields[$field_name]['public'])) {
                    $fields[$field_name]['public'] = true;
                }
            }
            return $fields;
        }
        return false;

    }

}

?>
