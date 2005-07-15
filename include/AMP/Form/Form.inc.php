<?php

/**********************
 *  AMPForm
 *
 *  a base class for entry forms using the HTML::QuickForm library
 *
 *  maintains common assumptions for field definition characteristics across
 *  modules
 *
 *  AMP v3.5.0
 *  2005-06-27
 *  Author: austin@radicaldesigns.org
 *
 **********/

 require_once( 'HTML/QuickForm.php' );
 require_once( 'AMP/Form/HTMLEditor.inc.php');
 require_once( 'AMP/Form/Template.inc.php');

 class AMPForm {

    var $fields;
        /******
         *  Converting a fields array into an HTML::QuickForm object is the
            main purpose of this class.
         *  Each field element is designated by a key which is the name
            and pointing to an array which may have one or more of these
            element types: 
         
         *  public:     must be TRUE for the field to appear unless the Build
                        method is called with $show_private_fields as TRUE
                        
         *  label:      text which describes the field contents

         *  size:       for text fields, the width; for multiselects, the height;
                        for textareas, a single value will be taken as the width;
                        a colon separated value will be taken as rows:columns;

         *  required:   form will not save to database until this field has a
                        value (enforceRequired() must be called by the controller)

         *  values:     set of values to choose from for select and group
                        elements

         *  constant:   value of the field will not accept change from the user
                        side

         *  default:    default value for the field

         *  template:   custom template for use by the HTML::QuickForm renderer

         *  attr:       attributes to be included in the HTML tag for the
                        element (e.g. style, checked, onclick )

         *  elements:   used for group elements to denote the sub-elements of
                        the group;

         *  type:       type of form element to create.  For a list of types,
                        see the HTML::QuickForm documentation.  
                        In addition, the library supports the following types:
                            multiselect :: a selectbox which allows more than one value
                            radiogroup  :: a set of radio buttons from which the user may select a single value
                            checkgroup  :: a set of checkboxes from which the user may select multiple values
                            wysiwyg     :: a instance of the Xinha htmlarea plugin
         *
         *
         ****************/

    // the order in which the fields are to be appended to the form
    var $fieldOrder;

    // the form object which is produced by the class
    var $form;
    var $formname;

    // the renderer of the form
    var $renderer;

    // the template factory
    var $template;

    // conditionals define arbitrary requirements of field definitions
    // the most common is 'public' == true;
    var $conditionals;

    // Register for any javascript required by the form elements
    var $javascript;
    var $javascript_register = array();

    var $submit_button = array(
        'submit' => array(
            'type' => 'submit',
            'enabled'=> true,
            'public'=> true,
            'label'=> 'Submit',
            'default'=>'submit')
        );


    function AMPForm( $name, $method="POST", $action = null ) {
        $this->init ( $name, $method, $action );
    }
    
    function init ( $name , $method="POST", $action = null ) {
        $this->formname = $name;
        $this->form = &new HTML_QuickForm( $name, $method, $action );
        $this->template = &new AMPFormTemplate();
    }

    function Build( $show_private_fields = false ) {
        if (!$show_private_fields) $this->setConditional( 'public', true );

        $this->_registerCustomElementTypes();
        $this->addFields(  $this->submit_button  );
		$this->renderer =& $this->form->defaultRenderer();

        if ( $this->hasFieldOrder() ) $this->_buildElements( $this->getFieldOrder() ); 
        $this->_buildElements();

        $this->enforceConstants();
        $this->setJavascript();
    }

    function output( $include_javascript = true ) {
        $script = "";
        if ($include_javascript) $script = $this->getJavascript();
        return  $this->form->display() . $script;
    }


    function setValues( $data ) {
        $this->form->setDefaults( $data );
    }

    function getValues ( $fields = null ) {
        return $this->form->exportValues( $fields );
    }

    function setJavascript() {
        $editor = &AMPFormElement_HTMLEditor::instance();
        if ($script = $editor->output()) $this->registerJavascript( $script, 'editor');

        if (empty($this->javascript_register)) return false;
        $this->javascript = join("\n", $this->javascript_register);
    }

    function registerJavascript( $script, $name = null ) {
        if (isset($name)) $this->javascript_register[ $name ] = $script;
        else $this->javascript_register[] = $script;
    }

    function getJavascript() {
        if (!isset($this->javascript)) return false;
        return $this->javascript;
    }

    #########################################
    ### Public Field Manipulation Methods ###
    #########################################

    function addFields ( $fieldset ) {
        if (!is_array($fieldset)) return false;
        foreach ( $fieldset as $key => $field_def ) {
            $this->fields[$key] = $field_def;
        }
        return true;
    }

    function setFieldValueSet( $fieldname, $valueset ) {
        if (!isset($this->fields[$fieldname])) return false;
        $this->fields[$fieldname]['values'] = $valueset;
    }

    function getField( $name ) {
        if (!isset( $this->fields[ $name ] )) return false;
        return $this->fields[$name];
    }

    function setFieldOrder( $fieldOrder ) {
        $this->fieldOrder = $fieldOrder;
    }

    function hasFieldOrder() {
        return isset( $this->fieldOrder );
    }

    function getFieldOrder() {
        if (isset( $this->fieldOrder )) return $this->fieldOrder;
        return array_keys( $this->fields );
    }

    function defineSubmit ( $value, $label = 'Submit' ) {
        $this->submit_button = array(
            $value => array(
                'type' => 'submit',
                'enabled'=> true,
                'public'=> true,
                'label'=> $label,
                'default'=>$value)
            );
    }

    function setConditional ( $key, $requirement ) {
        $this->conditionals[ $key ] = $requirement;
    }

    ######################################
    ### Public Form Validation Methods ###
    ######################################

    function validate() {
        return $this->form->validate();
    }

    function validateEmail( $email_field_name = "Email" ) {
        
		if ( $this->getField( $email_field_name) )
			$this->form->addRule( $email_field_name, 'Must be a valid email address.', 'email' );
    }

    function enforceRequiredFields() {
        foreach ($this->fields as $fname => $field_def) {
            if (isset($field_def['required']) && $field_def['required'] ) {
                $this->form->addRule( $fname, $field_def['label'] . ' is required.', 'required' );
                $this->form->_requiredNote = '<span style="color:#ff0000;">*</span> Required Field';
            }
        }
        return true;
    }

    function enforceConstants() {
        if (!isset($this->fields)) return false;
        $consts = array();
        foreach ($this->fields as $fname => $field_def) {
            if (isset($field_def['constant']) && $field_def['constant'] ) {
                $consts[$fname] = $field_def['default'];
            }
        }
        $this->form->setConstants( $consts );
    }


    #############################
    ### Privite Build Methods ###
    #############################

    function _registerCustomElementTypes() {
		$this->form->registerElementType('multiselect','HTML/QuickForm/select.php','HTML_QuickForm_select');
		$this->form->registerElementType('radiogroup','HTML/QuickForm/group.php','HTML_QuickForm_group');
		$this->form->registerElementType('checkgroup','HTML/QuickForm/group.php','HTML_QuickForm_group');
		$this->form->registerElementType('wysiwyg','HTML/QuickForm/textarea.php','HTML_QuickForm_textarea');
    }


    function _buildElements( $element_set = null ) {
        if (!isset($element_set)) $element_set = array_keys($this->fields);

        foreach ($element_set as $field_name ) {
            if (isset( $this->built_elements[ $field_name ] )) continue;

            $this->_addElement( $field_name );
            $this->built_elements[ $field_name ] = 1;
        }
    }


    function _addElement( $name ) {
        if (!( $field_def = $this->_confirmFieldDef($this->getField( $name )))) return false;

        $add_method     =   '_addElement'   . ucfirst( $field_def['type'] );
        $adjust_method  =   '_adjustElement'. ucfirst( $field_def['type'] );

        if (!method_exists ( $this, $add_method    )) $add_method    = "_addElementDefault"; 
        if (!method_exists ( $this, $adjust_method )) $adjust_method = false;

        $fRef = & $this->$add_method( $name, $field_def );
        if ($adjust_method) $this->$adjust_method( $fRef, $field_def );

		if ( isset( $field_def['attr'] ) && is_array( $field_def['attr'] ) ) {
			$fRef->updateAttributes($field_def['attr']);
		}

        if ( !$field_def['template'] ) return true;

		$template_function = ($field_def['type']=='header')?
                                'setHeaderTemplate':
                                'setElementTemplate';
		$this->renderer->$template_function( $field_def['template'], $name);

		return true;

	}

    function &_addElementDefault ( $name, $field_def ) {
        $defaults = $this->_getDefault( $name );
        return $this->form->addElement( $field_def['type'], $name, $field_def['label'], $defaults );
        
    }

    function &_addElementHtml( $name, $field_def ) {
        $name = $this->_getDefault( $name );
        return $this->form->addElement( 'html', $name );
    }

    function &_addElementSelect( $name, $field_def ) {
        $valueset = $this->_getValueSet( $name );
		if ( is_array( $valueset ) ) {
            $valueset = array('' => 'Select one') + $valueset;
        }
        return $this->form->addElement( 'select', $name, $field_def['label'], $valueset);
    }

    function &_addElementMultiselect( $name, $field_def ) {
        $size = $field_def['size'];
        $valueset = $this->_getValueSet( $name );
        $fRef = &$this->form->addElement( 'multiselect', $name, $field_def['label'], $valueset);
        $fRef->setMultiple(true);
        if ( $size ) $fRef->setSize( $size );
        return $fRef;
    }

    function &_addElementHeader( $name, $field_def ) {
        $defaults = $this->_getDefault( $name );
		if (strlen($defaults) > 0)  $label = $defaults;
        return $this->form->addElement( 'header', $name, $field_def['label']);
    }

    function &_addElementCheckgroup ( $name, $field_def ) {
		//Create sub-objects for group elements
        $valueSet = $this->_getValueSet( $name );
        $group_set = array();
        foreach ($valueSet as $def_key=>$def_value) {
                $group_set[] = HTML_QuickForm::createElement('checkbox',$def_key, null, $def_value);
        }
        return $this->form->addGroup( $group_set, $name, $field_def['label'], '<BR>');
    }

    function &_addElementRadiogroup ( $name, $field_def ) {
        $valueSet = $this->_getValueSet( $name );
        $group_set = array();
        foreach ($valueSet as $def_key=>$def_value) {
                $group_set[] = &HTML_QuickForm::createElement('radio', null, null, $def_value ,$def_key );
        }
        return $this->form->addGroup( $group_set, $name, $field_def['label'], '<BR>');
    }

    function &_addElementWysiwyg ( $name, $field_def ) {
        $field_def['type'] = 'textarea';
        $fRef = &$this->_addElementDefault( $name, $field_def );
        $fRef->updateAttributes ( array( "id"=>$name ) );
        $this->_adjustElementTextarea( $fRef, $field_def );
        $editor = &AMPFormElement_HTMLEditor::instance();
        $editor->addEditor( $name );
        return $fRef;
    }

    function _adjustElementCheckbox( &$fRef, $field_def ) {
        $fRef->setText( null );
    }

    function _adjustElementText( &$fRef, $field_def ) {
        if (!( $size = $field_def['size'])) return;
        $fRef->setSize( $size );
    }

    function _adjustElementTextarea ( &$fRef, $field_def ) {
		if (! ( $size = $field_def[ 'size' ] ) ) return false; 

        if ( strpos( $size, ':' ) ) {
            $tmpArray = split( ':', $size );
            $rows = $tmpArray['0'];
            $cols = $tmpArray['1'];
        } else {
            $cols = $size;
            $rows = 4;
        }

        if ( isset( $rows ) ) $fRef->setRows( $rows );
        if ( isset( $cols ) ) $fRef->setCols( $cols );
    }


    function _getDefault( $name ) {
        if (!($def = $this->getField( $name ))) return null;
        if (!isset($def['default'])) return null;
		return $this->fields[$name]['default'];
    }

    function _getValueSet ( $name ) {
        if (!($def = $this->getField( $name ))) return null;
        if (!isset($def['values'])) return null;
        return $def['values'];
    }

	function _getTemplate( $type=null ) {
        if (!isset($type)) return false;
        return $this->template->getTemplate( $type );
    }

    function _confirmFieldDef( $field_def ) {
        if (!$field_def) return false;
        if (!isset($field_def['type'])) return false;
		if ( !$this->_verifyConditionals( $field_def )) return false;

        $newdef = array();

		$newdef['type']     = (isset($field_def['type']))   ? $field_def['type']     : null;
		$newdef['required'] = (isset($field_def['required']))   ? $field_def['required']     : false;
		$newdef['label']    = (isset($field_def['label']))  ? $field_def[ 'label'  ] : null;
		$newdef['size']     = (isset($field_def['size']) && ($field_def['size'] != 0))   ? $field_def[ 'size' ]   : 40;
		$newdef['attr']     = (isset($field_def['attr']))   ? $field_def['attr']       : null;
		$newdef['elements'] = (isset($field_def['elements']))   ? $field_def['elements']       : null;
        $newdef['template'] = (isset($field_def['template'])? $field_def['template']: $this->_getTemplate( $newdef['type'] ));

        return $newdef;
    }

    function _verifyConditionals( $field_def ) {
        if (!isset( $this->conditionals )) return true;
        
        foreach ($this->conditionals as $key => $requirement ) {
            if ( $field_def [ $key ] != $requirement ) return false;
        }

        return true;
    }

 }
 ?>
