<?php

/*****
 *
 * AMP UserDataModule HTML_QuickForm builder Plugin
 *
 * Creates an HTML_QuickForm object based on the contents of
 * an UDM object.
 *
 *****/

require_once( 'AMP/Form/Form.inc.php' );
require_once( 'AMP/UserData/Plugin.inc.php' );

class UserDataPlugin_Build_QuickForm extends UserDataPlugin {
    var $AMPForm;

	var $options = array ('button_label'=>  array(
		'type'=>'text',
		'available'=>true,
		'label'=>'Label for Submit Button',
		'default'=>'Submit')
		);

    var $_formEngine;

	var $_field_attrs;
    var $available = true;  
    


    function UserDataPlugin_Build_QuickForm ( &$udm, $plugin_instance=null ) {
        $this->init( $udm, $plugin_instance );
    }

    function execute ( $options = null ) {
        $options = array_merge( $this->getOptions(), $options );

        $formEngine = & new AMPForm( $this->udm->name );

        //Create the Form
        $admin = $this->_adminConfig();
        $formEngine->addFields( $this->_setupUdmFields() );
        $formEngine->setFieldOrder( $this->udm->getFieldOrder() );
        $formEngine->defineSubmit( 'btnUdmSubmit', $options['button_label'] );
        $formEngine->Build( $admin );

        //set data Requirements
        $formEngine->validateEmail();
        if (!$admin) $formEngine->enforceRequiredFields();
        
        //Populate the form
		$formEngine->setValues( $this->udm->getStoredValues() );
		$formEngine->setValues( $this->udm->getData() );
		
		if($form_attrs = $this->getRegisteredFieldAttrs()) {
			foreach($form_attrs as $field => $field_attrs) {
				foreach($field_attrs as $attr) {
					$formEngine->addFieldAttr($field, $attr);
				}
			}
		}

        //register results with udm
		$this->udm->form = & $formEngine->form;
        if ($script = $formEngine->getJavascript()) {
            $this->_register_javascript( $script );
        }

		if ( $this->udm->submitted ) {
			if ( !$formEngine->validate() && !$admin ) {
                $this->udm->formInvalidCallback();
                return false;
			}
		}
        $this->_formEngine = &$formEngine;

		return $formEngine->form;
    }

	function registerFieldAttr($key, $attr) {
		$this->_field_attrs[$key][] = $attr;
	}

	function getRegisteredFieldAttrs() {
		return $this->_field_attrs;
	}

    function &getFormEngine( ) {
        return $this->_formEngine;
    }

    #############################
    ### Private Init Function ### 
    #############################

    function _register_fields_dynamic () {
		$this->fields['modin'] = array(
            'type' => 'hidden',
            'label'=> 'Module Instance',
            'public'=>  true,
            'enabled'=> true,
            'constant'=>true,
            'default' => $this->udm->instance);
    }

    #################################
    ### Private Utility Functions ###
    #################################

    function _adminConfig() {
        if (!$this->udm->admin) return false;

        //Allow admins to use Styles panel
        #$editor = &AMPFormElement_HTMLEditor::instance();
        #$editor->addPlugin('Stylist');

		//set PUBLISH field at the top of the form
		if ( $this->udm->_module_def['publish']) { 
            $this->udm->insertBeforeFieldOrder( array('publish') );
		}
        return true;
    }

    function _setupUdmFields() {
        $fields = & $this->udm->fields ;
        $newfields = array();
        foreach ( $fields as $fieldname => $field_def ) {
            if (!isset($field_def['enabled'])) continue;
            if (!$field_def['enabled']) continue;
            $newfields [$fieldname] = $this->_addValueSet( $fields[$fieldname] );
        }
        return $newfields;
    }

    function _addValueSet ( &$field_def ) {
        $newfield_def = $field_def;
        if (!($valueset = $this->getValueSet( $field_def ))) return $newfield_def;
            
        if (!is_array( $valueset ) ) return $this->_addDefault( $field_def, $valueset ); 

        $newfield_def['values'] = $valueset;
        return $newfield_def;
    }

    function _addDefault( &$field_def, $valueset ) {
        $newfield_def = $field_def;
        unset($newfield_def['values']);
        
        if (isset($field_def['default'])) return $newfield_def; 
        
        $field_def['default'] = $valueset;
        $newfield_def['default'] = $valueset;
        return $newfield_def;
    }

}
?>
