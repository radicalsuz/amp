<?php

require_once( 'AMP/System/Form/XML.inc.php');
require_once( 'Modules/WebAction/ComponentMap.inc.php');

class WebAction_Form extends AMPSystem_Form_XML {

    var $name_field = 'title';

    function WebAction_Form() {
        $name = 'webaction_define';
        $this->init( $name );
    }

    function setDynamicValues() {
        $this->addTranslation( 'enddate', '_makeDbDateTime', 'get');
        $this->addTranslation( 'modin', '_linkedIntroTexts', 'set');
        $this->setDefaultValue( 'modin', AMP_FORM_ID_WEBACTION_DEFAULT );
        $this->_linkedIntroTexts( array( 'modin' => AMP_FORM_ID_WEBACTION_DEFAULT ), 'modin');
     
    }

    function _linkedIntroTexts( $data, $fieldname ){
        require_once( 'AMP/UserData/Lookups.inc.php');
        $intro_text_set = array( 'intro_id', 'response_id', 'message_id', 'tellfriend_message_id');
        if ( !isset( $data[$fieldname])) return false;
        $new_values = FormLookup_IntroTexts::instance( $data[$fieldname] );
        if ( !count( $new_values )) return $data[$fieldname];

        foreach( $intro_text_set as $intro_select ){
            $this->setFieldValueSet( $intro_select, $new_values );
        }

        return $data[$fieldname];
        
    }
}
?>
