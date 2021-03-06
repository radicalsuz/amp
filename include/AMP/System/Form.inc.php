<?php
require_once ( 'AMP/Form/Form.inc.php' );
require_once ( 'AMP/System/Form/XMLFields.inc.php' );

class AMPSystem_Form extends AMPForm {

    var $id_field = 'id';
    var $name_field = 'name';
    var $allow_copy = true;
    var $_editor_fieldswap_object_id = 'HTML_Override_Hider';

    var $_submit_group = 'submitAction';
    var $submit_button = array( 'submitAction' => array(
        'type' => 'group',
        'elements'=> array(
            'save' => array(
                'type' => 'submit',
                'label' => 'Save Changes'),
            'cancel' => array(
                'type' => 'submit',
                'label' => 'Cancel'),
                /*
            'delete' => array(
                'type' => 'submit',
                'label' => 'Delete Record',
                'attr' => array ( 
                    'onclick' => 
                    "return confirmSubmit('Are you sure you want to DELETE this record?');" ),
                )
            */
            )
    ));

    var $source;


    function AMPSystem_Form ( $name, $method = "POST", $action = null ) {
        $this->init ( $name, $method, $action );
    }

    function init( $name, $method = "POST", $action = null ) {
        parent::init( $name, $method, $action );
        $this->_init_template( );
        $this->_init_submit( );
    }

    function _init_template( ) {
        $this->template->setClass( 'label', 'name' );
        $this->template->setClass( 'span', 'name' );
        $this->template->setClass( 'header', 'form_header' );
    }

    function _init_submit( ) {
        $this->defineSubmit( 
                'delete',
                'Delete Record',
                array ( 
                    'onclick' => 
                    "return confirmSubmit('".AMP_TEXT_RECORD_CONFIRM_DELETE."');" )
                );
        if ($this->allow_copy) $this->copy_button();
    }

    function defineSubmit( $value, $label = "Submit", $attr=null ) {
        $this->submit_button[ $this->_submit_group ]['elements'][$value] = 
            array(
                'type' => 'submit',
                'label'=> $label,
                'attr' => $attr 
            );
    }

    function removeSubmit ( $value ) {
        unset ($this->submit_button[ $this->_submit_group ]['elements'][$value] );
    }

    function Build() {
        parent::Build( true );
        $this->enforceRules();
    }

    function _allowedFields( $fields ) {
        $permission_manager = & AMPSystem_PermissionManager::instance();
        //clear non-allowed fields as specified by 'per' element in XML
        foreach( $fields as $fieldname => $field_def ) {
            if ( !isset( $field_def['per'] )) continue;
            $per_constant = $permission_manager->convertDescriptor( $field_def['per']);
            if ( !AMP_Authorized( $per_constant )) unset ( $fields[ $fieldname ] );
        }

        //clear non_allowed submit actions as specified by the map
        $map = &$this->_get_map( );
        foreach( $this->submit_button[ $this->_submit_group ]['elements'] as $action => $field_def ) {
            if ( !$map ) continue;
            if ( !$map->isAllowed( $action )) $this->removeSubmit( $action );
        }
        return $fields;
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
        $script_name = get_class( $this ) . '_getCopyName';
        $attr = array( "onclick" => "return " . $script_name . "();" );   
        $this->defineSubmit('copy', 'Save As...', $attr);
        $script = 
            'function '. $script_name .'() {
                pform = document.forms["'.$this->formname.'"];
                copyname = prompt ("What would you like to name this new item?");
                
                if (copyname != "" && copyname) {
                    pform.elements["'.$this->name_field.'"].value=copyname;
                    return true;
                }

                return false;
            }';
        $header = &AMP_getHeader( );
        $header->addJavascriptDynamic( $script, 'copy_button_' . $script_name );
    }

    function submitted() {
        if (!isset($_REQUEST[ $this->_submit_group ])) return false;
        $submitAction = $_REQUEST[ $this->_submit_group ];
        if (!is_array($submitAction)) return false;

        $key = key($submitAction);
        if (isset($this->submit_button[ $this->_submit_group ]['elements'][$key])) return $key;

        return false;
    }

    function getIdValue() {
        if (isset($_REQUEST[ $this->id_field ]) && is_numeric( $_REQUEST[ $this->id_field ])) return $_REQUEST[ $this->id_field ];
        if ( !isset( $this->form )) return false;
        if ( !$this->isBuilt ) return false;
        
        $set = $this->getValues( $this->id_field );
        if ($set) return $set[ $this->id_field ];
        return false;
    }

    function initNoId( ){
        $this->removeSubmit( 'copy' );
        $this->removeSubmit( 'delete' );
    }

    function getItemName() {
        if ( !$this->isBuilt ) $this->Build( );
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
        $this->_configHTMLEditor( $editor );
    }

    function _selectAddNull( $valueset, $name ) {
        if (!is_array($valueset)) return false;
        return array('' => 'None') + $valueset;
    }

    function _configHTMLEditor( &$editor ){
        //interface
    }

    function __wakeup( ) {
        parent::__wakeup( );
        if ($this->allow_copy) $this->copy_button();
    }

}
?>
