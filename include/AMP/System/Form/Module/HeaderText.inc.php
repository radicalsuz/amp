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

    function AMPSystem_IntroText_Form ( $name = "AMP_IntroText") {
        $this->init( $name );
        $this->addFields( $this->getFields());
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

    function getFields() {
        $moduleset = AMPSystem_Lookup::instance('Modules');
        $templateset = AMPSystem_Lookup::instance('Templates');
        $map = &AMPContent_Map::instance();
        $sections = $map->selectOptions();

        $fields['id'] = array(
            'type'  =>  'hidden' );
        $fields['modid'] = array (
            'type'  =>  'select',
            'label' =>  'Module',
            'required' => true,
            'values'=>  $moduleset );
        $fields['name'] =  array(
            'type'  =>  'text',
            'required' => true,
            'label' =>  'Page Name');
        $fields['title'] = array(
            'type'  =>  'text',
            'label' =>'Title',
            'public' => true );
        $fields['subtitle'] = array(
            'type'  =>  'text',
            'public' => true,
            'label' => 'Subtitle');
        $fields['body'] = array(
            'type'  =>  'wysiwyg',
            'label' => 'Text',
            'public' => true,
            'size'   => '20:65');
        $fields['html'] = array(
            'type'  =>  'checkbox',
            'label' => 'HTML Override');
        $fields['type'] = array(
            'type'  =>  'select',
            'label' => 'Section',
            'values' => $sections);
        $fields['templateid'] = array(
            'type'  =>  'select',
            'label' =>  'Template',
            'values' => $templateset );
        $fields['searchtype'] = array(
            'type'  =>  'text',
            'label' => 'Display URL');

        return $fields;
    }
}

?>
