<?php

require_once( 'AMP/System/Form/XML.inc.php');
require_once( 'Modules/WebAction/ComponentMap.inc.php');

class WebAction_Form extends AMPSystem_Form_XML {

    var $name_field = 'title';
    var $_intro_text_set = array( 'intro_id', 'response_id', 'message_id', 'tellfriend_message_id');
    var $_title_template = array( 'type' => 'text', 'size' => 50, 'required' => true, 'label' => 'Title');
    var $_text_template = array( 'type' => 'textarea', 'size' => '10:20', 'label' => 'Text');

    function WebAction_Form() {
        $name = 'webaction_define';
        $this->init( $name );
    }

    function setDynamicValues() {
        $this->addTranslation( 'enddate', '_makeDbDateTime', 'get');
        $this->addTranslation( 'modin', '_linkedIntroTexts', 'set');
        $this->setDefaultValue( 'modin', AMP_FORM_ID_WEBACTION_DEFAULT );
        $this->_linkedIntroTexts( array( 'modin' => AMP_FORM_ID_WEBACTION_DEFAULT ), 'modin');
        $this->_initBlankIntroTexts( );
     
    }

    function adjustFields( $fields ){
        $result_fields = array( );
        foreach( $fields as $field_name => $field_def ){
            $result_fields[$field_name] = $field_def;
        }
        foreach( $this->_intro_text_set as $introtext_field ){
            
        }
    }
    function _initBlankIntroTexts( ){
        foreach( $this->_intro_text_set as $introtext_field ){
            $this->addTranslation( $introtext_field, '_makeIntroTextEntryFields', 'set');
        }
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

    function _makeNewIntroText( $data, $fieldname ){
        if ( isset( $data[ $fieldname ])) return $data[ $fieldname ];
        if ( !isset( $data[ $fieldname . '_title'])) return false;
        require_once( 'AMP/System/Introtext.inc.php');

        $textItem = &new AMPSystem_IntroText( AMP_Registry::getDbcon( ));
        $textItem->setData( array( 
            'name'  =>  $this->_makeIntroTextName( $data, $fieldname ),
            'title' =>  $data[ $fieldname . '_title'],
            'body'  =>  $data[ $fieldnaem . '_text'] 
            ));
        $textItem->save( );
        return $textItem->id;

    }

    function _makeIntroTextName( $data, $fieldname ){
        return $data[$this->name_field] . ucwords( str_replace( array( '_', '_id'), array( ' ', '') , $fieldname ) );
    }
}
?>
