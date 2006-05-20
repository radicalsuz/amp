<?php

/**************
 *  AMPSystem_IntroText_Form
 *  a system-side form definition
 *  for moduletext
 *
 *  @version AMP 3.5.0
 *  @date 2005-06-27
 *  @author Austin Putman <austin@radicaldesigns.org>
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
        $fieldswapper = &ElementSwapScript::instance();
        $fieldswapper->addSwapper( $this->fieldswap_object_id );
        $fieldswapper->setForm( $this->formname, $this->fieldswap_object_id );
        $fieldswapper->addSet( 'no_editor', array('html'), $this->fieldswap_object_id ) ;
        $fieldswapper->setInitialValue( 'no_editor', $this->fieldswap_object_id );

        $this->registerJavascript( $fieldswapper->output() );

        $editor = & AMPFormElement_HTMLEditor::instance();
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
    function _formFooter( ){
        if ( !$this->getIdValue( ) ) return false;
        $renderer = &new AMPDisplay_HTML;
        return $renderer->inSpan( AMP_TEXT_SYSTEM_LINK_NAV_LAYOUT_EDIT, array( 'class' => 'intitle'))  
                . AMP_navCountDisplay_Introtext( $this->getIdValue( ) );
    }
}

?>
