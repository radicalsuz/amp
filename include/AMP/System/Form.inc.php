<?php
require_once ( 'AMP/Form/Form.inc.php' );
require_once ( 'AMP/System/Form/XMLFields.inc.php' );

class AMPSystem_Form extends AMPForm {

    var $id_field = 'id';
    var $name_field = 'name';
    var $allow_copy = true;
    var $_editor_fieldswap_object_id = 'HTML_Override_Hider';

    var $submit_button = array( 'submitAction' => array(
        'type' => 'group',
        'elements'=> array(
            'save' => array(
                'type' => 'submit',
                'label' => 'Save Changes'),
            'cancel' => array(
                'type' => 'submit',
                'label' => 'Cancel'),
            'delete' => array(
                'type' => 'submit',
                'label' => 'Delete Record',
                'attr' => array ( 
                    'onclick' => 
                    "return confirmSubmit('Are you sure you want to DELETE this record?');" ),
                )
            )
    ));

    var $source;


    function AMPSystem_Form ( $name, $method = "POST", $action = null ) {
        $this->init ( $name, $method, $action );
    }

    function init( $name, $method = "POST", $action = null ) {
        parent::init( $name, $method, $action );
        $this->template->setClass( 'label', 'name' );
        $this->template->setClass( 'span', 'name' );
        $this->template->setClass( 'header', 'intitle' );
        if ($this->allow_copy) $this->copy_button();
    }

    function defineSubmit( $value, $label = "Submit", $attr=null ) {
        $this->submit_button['submitAction']['elements'][$value] = 
            array(
                'type' => 'submit',
                'label'=> $label,
                'attr' => $attr 
            );
    }

    function removeSubmit ( $value ) {
        unset ($this->submit_button['submitAction']['elements'][$value] );
    }

    function Build() {
        parent::Build( true );
        $this->enforceRequiredFields();
    }

    function &_addElementGroup( $name, $field_def ) {
        $group_set = array();
        $default = $this->_getDefault( $name );
        foreach ( $field_def['elements'] as $el_name => $el_def ) {
            $fRef = &HTML_QuickForm::createElement( $el_def['type'], $el_name, $el_def['label'], $default );
            if (isset ($el_def['attr']) && $el_def['attr']) {
                $fRef->updateAttributes( $el_def['attr'] );
            }
            $group_set[] = & $fRef;
        }
        return $this->form->addGroup( $group_set, $name, $field_def['label'], '&nbsp;&nbsp;');

    }

    function copy_button () {
        $attr = array( "onclick" => "return AMPForm_getCopyName();" );   
        $this->defineSubmit('copy', 'Save As...', $attr);
        $script = 
        '<script type = "text/javascript">
        //<!--
            function AMPForm_getCopyName() {
                pform = document.forms["'.$this->formname.'"];
                copyname = prompt ("What would you like to name this new item?");
                
                if (copyname != "" && copyname) {
                    pform.elements["'.$this->name_field.'"].value=copyname;
                    return true;
                }

                return false;
            }
            //-->
            </script>';
        $this->registerJavascript( $script, 'copy_button' );
    }

    function submitted() {
        if (!isset($_REQUEST['submitAction'])) return false;
        $submitAction = $_REQUEST['submitAction'];
        if (!is_array($submitAction)) return false;

        $key = key($submitAction);
        if (isset($this->submit_button['submitAction']['elements'][$key])) return $key;

        return false;
    }

    function getIdValue() {
        if (!isset($_REQUEST['id'])) return false;
        return $_REQUEST['id'];
        /*
        $set = $this->getValues( 'id' );
        if (!$set) return false;
        return $set['id'];
        */
    }

    function getItemName() {
        if (!$this->getField( $this->name_field )) return false;
        $set = $this->getValues( array($this->name_field) );
        if (!$set) return false;
        return $set[$this->name_field];
    }

    function HTMLEditorSetup( $checkbox_fieldname = 'html') {
        require_once ('AMP/Form/ElementSwapScript.inc.php');
        $fieldswapper = &ElementSwapScript::instance();
        $fieldswapper->addSwapper( $this->_editor_fieldswap_object_id );
        $fieldswapper->setForm( $this->formname, $this->_editor_fieldswap_object_id );
        $fieldswapper->addSet( 'no_editor', array($checkbox_fieldname), $this->_editor_fieldswap_object_id ) ;
        $fieldswapper->setInitialValue( 'no_editor', $this->_editor_fieldswap_object_id );

        $this->registerJavascript( $fieldswapper->output() );

        $editor = & AMPFormElement_HTMLEditor::instance();
        $editor->register_config_action( 'ActivateSwap( window.'.$this->_editor_fieldswap_object_id.', "" );'); 
        $editor->register_config_action( 'document.forms["'.$this->formname.'"].elements["'.$checkbox_fieldname.'"].checked = true;' );
    }

}
?>
