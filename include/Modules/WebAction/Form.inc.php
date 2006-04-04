<?php

require_once( 'AMP/System/Form/XML.inc.php');
require_once( 'Modules/WebAction/ComponentMap.inc.php');

class WebAction_Form extends AMPSystem_Form_XML {

    var $name_field = 'name';
    var $_intro_text_set = array( 'intro_id', 'response_id', 'message_id', 'tellfriend_message_id');
    var $_title_template = array( 'type' => 'text', 'size' => 50, 'required' => false, 'label' => 'Title');
    var $_text_template = array( 'type' => 'textarea', 'size' => '10:50', 'label' => 'Text');
    var $_save_template = array( 'type' => 'submit', 'label' => 'Save');
    var $_switch_template = array( 'type' => 'blocktrigger', 'label' => 'Edit', 'block' => '_block');
    var $_fieldswapper;

    function WebAction_Form() {
        $name = 'webaction_define';
        $this->init( $name );
    }

    function setDynamicValues() {
        $this->addTranslation( 'enddate', '_makeDbDateTime', 'get');
        $this->addTranslation( 'modin', '_linkedIntroTexts', 'set');
        $this->setDefaultValue( 'modin', AMP_FORM_ID_WEBACTION_DEFAULT );
        $this->_linkedIntroTexts( array( 'modin' => AMP_FORM_ID_WEBACTION_DEFAULT ), 'modin');
        $this->_setupIntroTextValues( );
        $this->addTranslation( 'target_id', '_checkgroupFromText','set');
        $this->addTranslation( 'target_id', '_checkgroupToText','get');
        #$this->_initSwapFields( );
     
    }

    function _setupIntroTextValues( ){
        foreach( $this->_intro_text_set as $text_id_field ){
            $this->addTranslation( $text_id_field, '_populateIntroTextFields', 'set');
            $this->addTranslation( $text_id_field, '_saveIntroText', 'get');
        }
    }

    function _populateIntroTextFields( &$data, $fieldname ){
        if ( !isset( $data[$fieldname])) return false;
        $introtext_id = $data[$fieldname];
        if ( !$introtext_id ) return false;

        require_once( 'AMP/System/Introtext.inc.php');
        $introtext = &new AMPSystem_IntroText( AMP_Registry::getDbcon( ), $introtext_id );
        $data[ $fieldname . '_title' ] = $introtext->getTitle( );
        $data[ $fieldname . '_text' ] = $introtext->getBody( );

        return $data[$fieldname];
        
    }

    function _initSwapFields( ){
        require_once( 'AMP/Form/ElementSwapScript.inc.php');
        $this->_fieldswapper = &ElementSwapScript::instance( );
        foreach( $this->_intro_text_set as $textfield ) {
            $current_swap = $textfield . '_swap';
            $this->_fieldswapper->addSwapper( $current_swap );
            $this->_fieldswapper->setForm( $this->getFormName( ), $current_swap ) ;
            $this->_fieldswapper->addSet( 'basic', array( $textfield ), $current_swap );
            $this->_fieldswapper->addSet( 'new', array( $textfield . '_title', $textfield . '_text'), $current_swap );
            $this->_fieldswapper->setInitialValue( 'basic', $current_swap );
        }
        $this->registerJavascript( $this->_fieldswapper->output( ));
    }

    function adjustFields( $fields ) {
        $result_fields = array( );
        foreach( $fields as $field_name => $field_def ) {
            $result_fields[$field_name] = $field_def;
            if ( array_search( $field_name, $this->_intro_text_set ) === FALSE ) continue;

            $result_fields = array_merge( $result_fields, $this->_makeIntroTextEntryFields( $field_name, $field_def ));
        }
        return $result_fields;
    }

    function _linkedIntroTexts( $data, $fieldname ){
        require_once( 'AMP/UserData/Lookups.inc.php');
        if ( !isset( $data[$fieldname])) return false;
        $new_values = FormLookup_IntroTexts::instance( $data[$fieldname] );
        if ( !count( $new_values )) return $data[$fieldname];

        foreach( $this->_intro_text_set as $intro_select ){
            $this->setFieldValueSet( $intro_select, $new_values );
        }

        return $data[$fieldname];
        
    }

    function _makeIntroTextEntryFields( $fieldname , $base_fielddef ){
        $template_field = $this->_switch_template;
        $template_field['label'] = $this->_switch_template['label'] . " " . $base_fielddef['label'];
        $template_field['block'] = $fieldname . $this->_switch_template['block'];
        $new_fields[ $fieldname.'_block'] = $template_field;

        $template_field = $this->_title_template;
        $template_field['label'] = $base_fielddef['label'] . ' ' . $this->_title_template['label'];
        $template_field['block'] = $fieldname . $this->_switch_template['block'];
        $new_fields[$fieldname.'_title'] = $template_field;
        
        $template_field = $this->_text_template;
        $template_field['label'] = $base_fielddef['label'] . ' ' . $this->_text_template['label'];
        $template_field['block'] = $fieldname . $this->_switch_template['block'];
        $new_fields[ $fieldname.'_text'] = $template_field;

        /*
        $template_field = $this->_save_template;
        $template_field['label'] = $this->_save_template['label'] . ' ' . $base_fielddef['label'];
        $template_field['block'] = $fieldname . $this->_switch_template['block'];
        $new_fields[ $fieldname . '_save' ] = $template_field;
        */

        return $new_fields;
    }
    /*
    function _makeIntroTextEntryFields( $data, $fieldname ){

        if ( isset( $data[$fieldname])) return $data[$fieldname];
        if ( !( $base_def = $this->getField( $fieldname ))) return false;
        $this->dropField( $fieldname );

        $template_field = $this->_title_template;
        $template_field['label'] = $base_def['label'] . ' ' . $this->_title_template['label'];
        $new_fields[$fieldname.'_title'] = $template_field;
        
        $template_field = $this->_text_template;
        $template_field['label'] = $base_def['label'] . ' ' . $this->_text_template['label'];
        $new_fields[ $fieldname.'_text'] = $template_field;
        $this->addTranslation( $fieldname, '_makeNewIntroText', 'get');
        $this->insertBeforeFieldOrder( array_keys( $new_fields ), $fieldname );
        $this->addFields( $new_fields );
    }
    */

    function _saveIntroText( $data, $fieldname ){
        $start_id = isset( $data[ $fieldname ]) ? $data[ $fieldname ] : false;
        if ( !( isset( $data[ $fieldname . '_title']) && $data[$fieldname.'_title'])) return $start_id;

        require_once( 'AMP/System/Introtext.inc.php');
        $text_data = array( 
            'title' =>  $data[ $fieldname . '_title'],
            'body'  =>  $data[ $fieldname . '_text'] 
            );

        $tools_lookup = AMPSystem_Lookup::instance( 'toolsbyForm');
        if ( !isset( $tools_lookup[ $data['modin' ]])) return $data ;
        $text_data['modid'] = $tools_lookup[ $data['modin'] ];


        if ( !$start_id ) {
            $text_data['name']  = $this->_makeIntroTextName( $data, $fieldname );
        } else {
            $text_data['id'] = $start_id;
        }

        $textItem = &new AMPSystem_IntroText( AMP_Registry::getDbcon( ));
        $textItem->setData( $text_data );
        $textItem->save( );

        return $textItem->id;

    }

    function _makeIntroTextName( $data, $fieldname ){
        return ucwords( AMP_TEXT_ACTION . ' ' . str_replace( array( '_id', '_'), array( '', ' ') , $fieldname ) ) . ': ' .$data[$this->name_field];
    }
}

?>
