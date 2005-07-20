<?php

/**************
 *  AMPSystem_IntroText_Form
 *  a system-side form definition
 *  for moduletext
 *
 *  AMP 3.5.0
 *  2005-06-27
 *  Author: austin@radicaldesigns.org
 */


require_once ('AMP/System/Form.inc.php');
require_once ('AMP/Form/ElementSwapScript.inc.php');

class AMPSystem_IntroText_Form extends AMPSystem_Form {

    var $fieldswap_object_id = 'HTML_Override_Hider';
    var $AMP_Object_Type = "IntroText";

    function AMPSystem_IntroText_Form () {
        $name = "AMP_IntroText"; 
        $this->init( $name );
        if ($this->addFields( $this->readFields())) {
            $this->setDynamicValues();
        }
        $this->HTMLEditorSetup();
    }

    function HTMLEditorSetup() {
        $fieldswapper = & new ElementSwapScript( $this->fieldswap_object_id );
        $fieldswapper->formname = $this->formname;
        $fieldswapper->addSet( 'no_editor', array('html')) ;
        $fieldswapper->initial_set = 'no_editor';

        $this->registerJavascript( $fieldswapper->output() );

        $editor = & AMPFormElement_HTMLEditor::instance();
        #$editor->width=700;
        #$editor->addPlugin('Stylist');
        $editor->register_config_action( 'ActivateSwap( window.'.$this->fieldswap_object_id.', "" );'); 
        $editor->register_config_action( 'document.forms["'.$this->formname.'"].elements["html"].checked = true;' );
    }

    function setDynamicValues() {
        $map = &AMPContent_Map::instance();
        $this->setFieldValueSet( 'modid',      AMPSystem_Lookup::instance('Modules'));
        $this->setFieldValueSet( 'templateid', AMPSystem_Lookup::instance('Templates'));
        $this->setFieldValueSet( 'type',       $map->selectOptions());
    }

    function readFields() {

        $fieldsource = & new AMPSystem_Form_XMLFields( $this->AMP_Object_Type, 'Fields' );

        if ( $fields = $fieldsource->readData() )     return $fields;

        return false;

    }
}

?>
