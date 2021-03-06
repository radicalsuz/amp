<?php

/**
 *  AMPForm
 *
 *  a base class for entry forms using the HTML::QuickForm library
 *
 *  maintains common assumptions for field definition characteristics across
 *  modules
 *
 *  @version AMP v3.5.0
 *  @date 2005-06-27
 *  @author  Austin Putman <austin@radicaldesigns.org>
 *
 */

define('AMP_FORM_UPLOAD_MAX',33554432);
 require_once( 'HTML/QuickForm.php' );
 require_once( 'AMP/Form/HTMLEditor.inc.php');
 require_once( 'AMP/Form/Template.inc.php');
require_once( 'HTML/QuickForm/Renderer/Default.php');
foreach( $GLOBALS['HTML_QUICKFORM_ELEMENT_TYPES'] as $type => $def ) {
    include_once( $def[0]);
}

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
                        elements.  can be specified as an array, or an array in the form <key> <value>

         *  lookup:     provides a dynamic set of values for select/group elements
         *      instance:   the name of the lookup
         *      module:     the type of lookup, currently ampsystem( default ), content, or form
         *      instance_var:        an optional variable to narrow the range of values returned
         
         *  block:      include this element in a block set
         
         *  block_trigger: display the block named for this element if this value is set

         *  constant:   value of the field will not accept change from the user
                        side

         *  default:    default value for the field

         *  template:   custom template for use by the HTML::QuickForm renderer

         *  attr:       attributes to be included in the HTML tag for the
                        element (e.g. style, checked, onclick )

         *  elements:   used for group elements to denote the sub-elements of
                        the group;

         *  options:    used for date elements to specify characteristics

         *  type:       type of form element to create.  For a list of types,
                        see the HTML::QuickForm documentation.  
                        In addition, the library supports the following types:
                            multiselect :: a selectbox which allows more than one value
                            radiogroup  :: a set of radio buttons from which the user may select a single value
                            checkgroup  :: a set of checkboxes from which the user may select multiple values
                            wysiwyg     :: an instance of the Xinha htmlarea plugin
                            blocktrigger:: a DHTML envelope for a set of elements
                            imagepicker :: a selectbox that pulls from AMP's image library and displays a preview of the selected image
                            captcha     :: an image containing text that must be matched by the user for the form to submit

         *  rules:     rules the element must fulfill during form validation, supplied as an array eg( 'type'=> 'email', 'message' => 'Must be a valid email address')
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
    var $_current_block = "";

    // conditionals define arbitrary requirements of field definitions
    // the most common is 'public' == true;
    var $conditionals;

    // Register for any javascript required by the form elements
    var $javascript;
    var $javascript_register = array();

	var $translations = array();

    var $submit_button = array(
        'submit' => array(
            'type' => 'submit',
            'enabled'=> true,
            'public'=> true,
            'label'=> 'Submit',
            'default'=>'submit')
        );

    var $isBuilt=false;
    var $_fileNames = array( );

    /**
     * _has_captcha 
     *
     * one captcha per customer, please
     * 
     * @var mixed
     * @access protected
     */
    var $_has_captcha = false;


    function AMPForm( $name, $method="POST", $action = null ) {
        $this->init ( $name, $method, $action );
    }
    
    function init ( $name , $method="POST", $action = null ) {
        $this->formname = $name;
        if (!isset($action)) $action = PHP_SELF_QUERY();
        $this->form = &new HTML_QuickForm( $name, $method, $action );
        $this->template = &new AMPFormTemplate();
        $this->_after_init( );
    }

    function _after_init( ){
        //interface
    }

    function Build( $show_private_fields = false ) {
        if (!$show_private_fields) $this->setConditional( 'public', true );

        $this->_registerCustomElementTypes();
        $this->addFields( $this->render_bot_catcher( ));
        $this->addFields(  $this->submit_button  );
        if ( method_exists( $this, '_pastSubmitElements' )) $this->addFields ( $this->_pastSubmitElements() );
		$this->renderer =& $this->form->defaultRenderer();

        if ( $this->hasFieldOrder() ) $this->_buildElements( $this->getFieldOrder() ); 
        $this->_buildElements();

        $this->enforceConstants();
        $this->setJavascript();
        $this->isBuilt = true;
    }

    function _initJavascriptActions( ){
        //interface
    }

    function output( $include_javascript = true ) {
        $form_footer = "";
        $script = "";

        if ( $include_javascript ) {
            $script = $this->getJavascript();
        }
        $form_header = $this->_formHeader();
        $form_footer = $this->_formFooter();

        $output = $form_header 
                . $this->form->display() 
                . $form_footer 
                . $script;
		return $output;
    }

    function render_bot_catcher( ) {
        return array( 
            ('First_Name_'.date('Y'))=> 
                array(  'type' => 'text', 
                        'label' => 'First Name', 
                        'enabled' => true, 
                        'public' => true, 
                        'rules' => array( array( 'type' => 'blank', 'message' => 'this field is for machine use only' )),
                        'template' => 
                            "\n\t<tr class='AMPComponent_hidden'>\n\t\t<td align=\"right\" valign=\"top\" class=\"%s\">
                            <!-- BEGIN required --><span style=\"color: #ff0000\">*</span><!-- END required -->
                            {label}</td>\n\t\t<td valign=\"top\" align=\"left\" class=\"%s\">
                            <!-- BEGIN error --><span style=\"color: #ff0000\">{error}</span><br /><!-- END error -->\t
                            {element}</td>\n\t</tr>",
                        ));
    }

    function execute( ){
        return $this->output( );
    }

    function _formHeader( ){
        return false;
    }

    function _formFooter( ){
        return false;
    }

    function setValues( $data ) {
        $this->form->setDefaults( $this->translate($data, 'set') );
    }

    function applyDefaults() {
        $default_set = array();
        $types_to_avoid = array( "button", "submit" );

        foreach ($this->fields as $fname => $fDef) {
            if ( !isset( $fDef['type'])) trigger_error( $fname . ' type not set' );
            if ( array_search( $fDef['type'], $types_to_avoid) !== FALSE ) continue;
            if ( !( $value = $this->_getDefault( $fname ))) continue; 
            $default_set[ $fname ] = $value;
        }

        $this->setValues( $default_set );
    }

    function setDefaultValue( $fieldname, $value ) {
        $this->fields[ $fieldname ]['default'] = $value;
        if (! ($this->isBuilt && ($fRef= &$this->form->getElement( $fieldname )))) return true;

        $current = $fRef->getValue();
        $current_default = $this->_getDefault( $fieldname );
        if ((!(isset($current)||isset($current_default))) 
            || ($current_default == $current)) $fRef->setValue( $value );
    }

    function getValues ( $fields = null ) {
        $requested_fields = $fields;
        if ( isset( $fields ) && !is_array( $fields )) $requested_fields = array( $fields );
        if ( is_object( $fields )) $requested_fields = null;

        return $this->translate( $this->form->exportValues( $fields ), 'get', $requested_fields );
    }

	function translate ( $data, $action = "get", $requested_fields = null ) {
        if ( is_object( $data ) && strtolower( get_class( $data )) == 'html_quickform_error') {
            trigger_error( $data->getMessage( ));
            return false;
        }
		if (empty( $this->translations) || empty( $this->translations[ $action ])) return $data;

		$result_data = $data;
		foreach ( $this->translations[ $action] as $fieldname => $translate_method_set ) {
            if ( !is_array( $translate_method_set )) continue;
            if ( isset( $requested_fields ) && !in_array( $fieldname, $requested_fields )) continue;

            foreach( $translate_method_set as $translate_method ){
                if (!method_exists( $this, $translate_method )) continue;
                $result_data[ $fieldname ] = $this->$translate_method( $result_data, $fieldname );
            }
		}
		return $result_data;
	}

    function setJavascript() {

        /*
        $header = &AMP_getHeader( );
        if ($script = $editor->output()) $header->addJavascriptDynamicPrefix( $script, 'editor' );
            //$this->registerJavascript( $script, 'editor');
        */

        if (empty($this->javascript_register)) return false;
        $this->javascript = join("\n", $this->javascript_register);
    }

    function registerJavascript( $script, $name = null ) {
        if (isset($name)) $this->javascript_register[ $name ] = $script;
        else $this->javascript_register[] = $script;
    }

    function getJavascript() {
        $this->_initJavascriptActions( );
        $editor = &AMPFormElement_HTMLEditor::instance();
        $editor->output( );

        if (!isset($this->javascript)) return false;
        return $this->javascript;
    }

    function initAjaxUpdate( ){
        $pageHeader = &AMP_getHeader( );
        $pageHeader->addJavaScript( '/scripts/ajax/prototype.js', 'prototype');
        $pageHeader->addJavaScript( '/scripts/ajax/scriptaculous.js', 'scriptaculous');

        $this->form->updateAttributes( array( 'onSubmit' => 'Ajax.InLineForm.onSubmit( );', 'class' => 'inplaceeditor-form'));
    }

    function initNoId( ){
        //interface
    }

    #########################################
    ### Public Field Manipulation Methods ###
    #########################################

    function addFields ( $fieldset ) {
        if (!is_array($fieldset)) return false;
        $fieldset = $this->_allowedFields( $fieldset );

        foreach ( $fieldset as $key => $field_def ) {
            $this->addField( $field_def, $key );
        }
        if ($this->isBuilt) $this->_buildElements();
        return true;
    }

    function addField ( $field_def, $name ) {
        $this->fields[ $name ] = $field_def;

        if ($this->isBuilt)  $this->_rebuildFormField( $name );
    }

    function _rebuildFormField( $fieldname ) {
        if (!$this->isBuilt)  return;
        if (!$current_field = &$this->form->getElement( $fieldname )) return;

        $temp_fieldname = $fieldname. '_temp';
        $this->fields[$temp_fieldname] = $this->fields[ $fieldname ];

        $this->_addElement( $temp_fieldname );
        $this->form->insertElementBefore( $this->form->removeElement( $temp_fieldname ), $fieldname );
        $this->form->removeElement( $fieldname );

        unset($this->fields[ $temp_fieldname ] );
        $this->_addElement( $fieldname );
        $this->form->insertElementBefore( $this->form->removeElement( $fieldname ), $temp_fieldname );
        $this->form->removeElement( $temp_fieldname );
    }

    function _manageUpload( $data, $filefield ) {
        if ( isset( $this->_fileNames[$filefield] )) return $this->_fileNames[ $filefield ];
        if (!( isset( $_FILES[ $filefield ][ 'tmp_name' ] ) && $_FILES[$filefield]['tmp_name'])) {
            if ( !isset( $data[ $filefield.'_value' ])) return false; 
            $this->_fileNames[ $filefield ] = $data[ $filefield.'_value' ];
            return $this->_fileNames[ $filefield ];
        }

        require_once( 'AMP/System/Upload.inc.php' );
        $upLoader = &new AMPSystem_Upload( $_FILES[ $filefield ][ 'name' ] );
        $this->_initUploader( $data, $filefield, $upLoader );
        if (!$upLoader->execute( $_FILES[ $filefield ][ 'tmp_name' ] )) return false;

        require_once( 'AMP/Content/Image/Resize.inc.php');
        $reSizer = &new ContentImage_Resize();
        if ( $reSizer->setImageFile( $upLoader->getTargetPath( ))) {
            $reSizer->execute( );
            AMP_lookup_clear_cached( 'images');
        }

        $this->_fileNames[ $filefield ] = basename( $upLoader->getTargetPath() );
        return $this->_fileNames[ $filefield ];
    }

    function _initUploader( $data, $filefield, &$uploader ) {
        //interface
    }

    function _addFileLink( $data, $fieldname ) {
        if (!( isset($data[ $fieldname ] ) && $data[ $fieldname ])) return null;
        require_once( 'AMP/Content/Article/DocumentLink.inc.php' );

        $fileLink = &new DocumentLink( $data[$fieldname] ); 
        if ( strpos( $this->fields[$fieldname]['label'], $data[ $fieldname ] ) === FALSE ) {
            $this->setFieldLabel( $fieldname, $this->fields[$fieldname]['label'] . $fileLink->display( 'div' ) );
        }

        $this->form->setDefaults( array( $fieldname.'_value' => $data[$fieldname] ));
    }

    function _addHiddenField( $fieldname, $attr = array( ) ) {
        $this->addFields( array(  $fieldname => array( 'type'=>'hidden', 'enabled'=>true, 'public'=>true, 'attr' => $attr )));
    }

	function _makeDbDateTime( $data, $fieldname ) {
        if ( !isset( $data[$fieldname])) return false;
        if ( !is_array( $data[$fieldname])) return false;
        $value = $data[ $fieldname ];

        $month  = isset($value['M'])&&$value['M']? $value['M']:(isset($value['m'])&&$value['m']?$value['m']:0);
        $day    = isset($value['D'])&&$value['D']? $value['D']:(isset($value['d'])&&$value['d']?$value['d']:false);
        $year   = isset($value['Y'])&&$value['Y']? $value['Y']:(isset($value['y'])&&$value['y']?$value['y']:0);
        $hour   = isset($value['H'])&&$value['H']? $value['H']:0;
        $hour   = isset($value['h'])&&$value['h']? $value['h']:$hour;
        $minute = isset($value['i'])&&$value['i']? $value['i']:0;
        $second = isset($value['s'])&&$value['s']? $value['s']:0;

		if ( isset( $value['a']) && ( $value['a'] == 'pm')) $hour+=12;
        $time_stamped = mktime($hour,$minute,$second,$month,$day,$year);
        if ( !$time_stamped ) return false;
        $dat_is_good = date( 'Y-m-d H:i:s', $time_stamped );

        if ( !AMP_verifyDateTimeValue( date( 'Y-m-d H:i:s', $time_stamped ))) return AMP_NULL_DATETIME_VALUE_DB;
        if ( !AMP_verifyDateValue( date( 'Y-m-d', $time_stamped ))) return AMP_NULL_DATETIME_VALUE_DB;

		return date( 'YmdHis', $time_stamped );
	}

    function _checkgroupToArray( $data, $fieldname ){
        if ( !( isset( $data[$fieldname]) && is_array( $data[$fieldname]))) return false;
        return array_keys( $data[ $fieldname ]);
    }
    function _checkgroupFromArray( $data, $fieldname ){
        if ( !( isset( $data[$fieldname]) && is_array( $data[$fieldname]))) return false;
        $results = array( );
        foreach( $data[$fieldname] as $cg_key ) {
            $results[$cg_key] = 1;
        }
        return $results;
    }
    function _checkgroupToText( $data, $fieldname ){
        if ( !( $values = $this->_checkgroupToArray( $data, $fieldname ))) return false;
        return join( ',', $values);
    }
    function _checkgroupFromText( $data, $fieldname ){
        $data[ $fieldname ] = preg_split( '/\s?,\s?/', $data[$fieldname] );
        return $this->_checkgroupFromArray( $data, $fieldname );
    }

    function _multiselectToText( $data, $fieldname ){
        if ( !( isset( $data[$fieldname]) && is_array( $data[$fieldname]))) return false;
        return join( ',', $data[ $fieldname ]);
    }

    function dropField( $fieldname ) {
        if (!isset($this->fields[ $fieldname ])) return false; 
        unset ( $this->fields[ $fieldname ] );
        if ( isset( $this->form )) {
            $this->form->removeElement( $fieldname );
        }
        return true; 
    }

	function addTranslation( $fieldname, $method, $action="get" ) {
        if ( is_array( $this->translations ) 
                && isset( $this->translations[$action]) 
                && isset( $this->translations[$action][$fieldname]) 
                && array_search( $method, $this->translations[$action][$fieldname]) !== FALSE ) {
                    return;
        }
		$this->translations[$action][ $fieldname ][] = $method;
	}

    function setFieldValueSet( $fieldname, $valueset ) {
        if (!isset($this->fields[$fieldname])) return false;
        $this->fields[$fieldname]['values'] = $valueset;

        if ($this->isBuilt && ($fRef= &$this->form->getElement( $fieldname ))) {
            $fRef->_options = array( );
            $fRef->loadArray( $this->_selectAddNull($valueset, $fieldname ) );
        }
            
    }

    function setFieldLabel ( $fieldname, $label, $override = false ) {
        if ( !$override ) {
            if (!isset($this->fields[$fieldname])) return false;
            $this->fields[$fieldname]['label'] = $label;
        }

        if ($this->isBuilt && ($fRef= &$this->form->getElement( $fieldname ))) {
            $fRef->setLabel( $label );
        }
            
    }

    function addToFieldValueSet( $fieldname, $valueset) {
        $full_valueset=$valueset;
        if (is_array( $this->fields[$fieldname]['values'])) {
            $full_valueset = array_merge( $this->fields[ $fieldname ][ 'values'], $full_valueset );
        }
        $this->fields[$fieldname]['values'] = $full_valueset;
        if ($this->isBuilt && ($fRef= &$this->form->getElement( $fieldname ))) {
            $fRef->loadArray( $valueset );
        }
    }

    function addFieldAttr ( $fieldname, $attr ) {
        if (!isset($this->fields[$fieldname])) return false;
        $this->fields[$fieldname]['attr'] = $attr;
        if ($this->isBuilt && ($fRef= &$this->form->getElement( $fieldname ))) {
            $fRef->updateAttributes( $attr );
        }
    }


    function getField( $name ) {
        if (!isset( $this->fields[ $name ] )) return false;
        return $this->fields[$name];
    }

    function getFields() {
        return $this->fields;
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

     function insertBeforeFieldOrder ( $fields, $beforeField = 0 ) {
        $fieldOrderSet = $this->getFieldOrder();
        $startinsert = 0;

        if (is_numeric($beforeField) && ($beforeField!=0)) $startinsert = $beforeField;
        elseif ($key = array_search($beforeField, $fieldOrderSet)) $startinsert = $key;

        if ($startinsert) {
            $newfieldOrder = array_slice($fieldOrderSet, 0, $startinsert);
            $fieldOrderSet = array_slice($fieldOrderSet, $startinsert);
        }

        foreach ( $fields as $fieldname ) {
            $newfieldOrder[] = $fieldname;
        }

        foreach ($fieldOrderSet as $fieldname) {
            $newfieldOrder[] = $fieldname;
        }

        $this->setFieldOrder( $newfieldOrder );
     }

     function insertAfterFieldOrder( $fields ) {
        $fieldOrderSet = $this->getFieldOrder();

        foreach ( $fields as $fieldname ) {
            $fieldOrderSet[] = $fieldname;
        }
        $this->setFieldOrder( $fieldOrderSet );
     }

    function defineSubmit ( $value, $label = 'Submit' ) {

        $this->submit_button = array(
            $value => array(
                'type' => 'submit',
                'separator' => 'endform',
                'public'=> true,
                'label'=> $label,
                'default'=>$value)
            );

        if (method_exists( $this, 'adjustSubmit' )) $this->adjustSubmit();
    }

    function setConditional ( $key, $requirement ) {
        $this->conditionals[ $key ] = $requirement;
    }

    function getFormName() {
        return $this->formname;
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
                $label = isset( $field_def['label']) ? $field_def['label'] : false;
                $this->form->addRule( $fname, $label . ' is required.', 'required' );
                $this->form->_requiredNote = '<span style="color:#ff0000;">*</span> Required Field';
            }
        }
        return true;
    }

    function enforceRules( ) {
        foreach ($this->fields as $fname => $field_def) {
            if (isset($field_def['rules']) && is_array( $field_def['rules'] ) && !empty( $field_def['rules'])) {
                foreach( $field_def['rules'] as $rule_def ) {
                    if ( !isset( $rule_def['type']) && $rule_def['type']) continue;
                    if ( !isset( $rule_def['message']) && $rule_def['message']) continue;
                    $this->form->addRule( $fname, $rule_def['message'], strtolower( $rule_def['type'] ) );
                }
            }
        }
        $this->enforceRequiredFields( );
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
		$this->form->registerElementType('captcha','HTML/QuickForm/text.php','HTML_QuickForm_text');
    }


    function _buildElements( $element_set = null ) {
        if (!isset($element_set)) $element_set = array_keys($this->fields);

        foreach ($element_set as $field_name ) {
            if (isset( $this->built_elements[ $field_name ] )) continue;

            $this->_addElement( $field_name );
            $this->built_elements[ $field_name ] = 1;
        }
    }


    function _addElement( $name, $field_def = null ) {
        if (!( isset( $field_def ) || $field_def = $this->_confirmFieldDef($this->getField( $name )))) return false;

        $add_method     =   '_addElement'   . ucfirst( $field_def['type'] );
        $adjust_method  =   '_adjustElement'. ucfirst( $field_def['type'] );

        if (! ( method_exists ( $this, $add_method    ) && $field_def['type']) ) $add_method    = "_addElementDefault"; 
        if (!method_exists ( $this, $adjust_method )) $adjust_method = false;

        $fRef = & $this->$add_method( $name, $field_def );
        if ( !$fRef ) return false;
        if ($adjust_method) $this->$adjust_method( $fRef, $field_def );

		if ( isset( $field_def['attr'] ) && is_array( $field_def['attr'] ) ) {
			$fRef->updateAttributes($field_def['attr']);
		}

        if ( !$field_def['template'] ) return true;
        //if ( $name == 'author' ) print $field_def['template'] . 'KK <BR>';

		$this->renderer->setElementTemplate( $field_def['template'], $name);

		return true;

	}

    function &_addElementDefault ( $name, $field_def ) {
        $defaults = $this->_getDefault( $name );
        return $this->form->addElement( $field_def['type'], $name, $field_def['label'], $defaults );
        
    }

    function &_addElementFile ( $name, $field_def ) {
        $this->addTranslation( $name, '_manageUpload', 'get' );
        $this->addTranslation( $name, '_addFileLink', 'set' );
        $this->_addFileValue( $name );
		$this->form->setMaxFileSize(AMP_FORM_UPLOAD_MAX);

        $defaults = $this->_getDefault( $name );
        return $this->form->addElement( 'file', $name, $field_def['label'], $defaults );

    }

    function &_addElementCaptcha ( $name, $field_def ) {
        $false = false;
        if ( $this->_has_captcha ) return $false;
        $this->_has_captcha = true;

        $this->_addUniqueIdValue( );

        require_once('AMP/Content/Display/HTML.inc.php');
        $renderer = &new AMPDisplay_HTML;
        $this->form->addElement( 
                'static', 'captcha_'.$name, "",  
                $renderer->image( AMP_Url_AddVars( AMP_CONTENT_URL_CAPTCHA, array( 'key='. AMP_SYSTEM_UNIQUE_VISITOR_ID ) ), array( 'align' => 'center'))
                );

        $defaults = $this->_getDefault( $name );
        $fRef = &$this->form->addElement( 'text', $name, $field_def['label'], $defaults );

        require_once( 'AMP/Form/Element/Captcha.inc.php');
        $captcha_demo = &new PhpCaptcha( array( ) );
        $rule_config = array( $captcha_demo, 'Validate' );
        $this->form->addRule( $name, AMP_TEXT_ERROR_FORM_CAPTCHA_FAILED, 'callback', $rule_config );

        return $fRef;
    }

    function _addUniqueIdValue( ) {
        $this->form->addElement(  'hidden', 'AMP_SYSTEM_UNIQUE_VISITOR_ID', AMP_SYSTEM_UNIQUE_VISITOR_ID );

    }

    function _addFileValue( $name ){
        $this->form->addElement(  'hidden', $name.'_value');

    }

    function &_addElementBlocktrigger( $name, $field_def ) {
        $jscript = "change_form_block( '".$field_def['block']."' );";
        $div = "";
        if ( isset( $field_def['block_trigger'])){
            $trigger_set = is_array( $field_def['block_trigger']) ? 
                                        $field_def['block_trigger'] : array(  $field_def['block_trigger'] );
            foreach( $trigger_set as $trigger ){
                $this->addTranslation($trigger,'_showDynamicBlock', 'set');   
            }
        }
		$name_block =   "<a href=\"javascript:$jscript\" class=\"trigger\">"
                        . "<img src=\"images/arrow-right.gif\" border=\"0\" class=\"field_arrow\" id=\"arrow_".$field_def['block'] ."\" />"
                        . $field_def['label'] ."</a>";
        $this->setDefaultValue( $name, $name_block );
        return $this->_addElementHtml( $name, $field_def );
    }

    function _showDynamicBlock( $data, $fieldname ) {
        $def = $this->getField( $fieldname );
        if ( !( isset( $data[$fieldname]) && $data[$fieldname ] )) return false;
        if ( ! isset( $def['block'])) return $data[$fieldname];

        $script = 'change_form_block( "'.$def['block'].'");';
        $header = &AMP_getHeader( );
        $header->addJavascriptOnload( $script, 'form_block_'.$def['block'] );
        return $data[$fieldname];
    }


    function &_addElementImagepicker( $name, $field_def ){
        $this->addTranslation( $name, '_addImageLink', 'set' );
        return $this->_addImageSelect( $name, $field_def );
    }

    function _addImageLink( $data, $fieldname ) {
        
        $displayfield_name = $fieldname . '_display';
        require_once( 'AMP/Form/ImageLink.inc.php' );

        $fileLink = &new ImageLink( );
        if ( isset( $data[$fieldname] ) ) $fileLink->setFile( $data[$fieldname]); 
        $fileLink->setImageId( $displayfield_name );
        $this->form->setDefaults( array( $displayfield_name  => $fileLink->display( 'div' ) ));

        if (!( isset($data[ $fieldname ] ) && $data[ $fieldname ])) return null;
        $valuefield_name = $fieldname;# . '_value';
        $this->form->setDefaults( array( $valuefield_name  => $data[$fieldname] ));
    }

    function _addImageDisplay( $fieldname ){

        $displayfield_name = $fieldname . '_display';

        if ( !$field_def = $this->getField( $displayfield_name )){
            $basefield = array( 'type'  =>  'static', 'default' => null);
            $field_def = $this->getField( $fieldname );
            if ( isset( $field_def['block'])) $basefield['block'] = $field_def['block'];
            
            $this->addField( $basefield, $displayfield_name);
            $this->_addElement( $displayfield_name );
        }
        return $displayfield_name;
    }

    function &_addImageSelect( $name, $field_def ) {
        $valuefield_name = $name;
        $display_name = $this->_addImageDisplay( $name );
        $picker = &$this->form->addElement(  'select', $valuefield_name, $field_def['label'], AMP_lookup( 'images') );

        $srcpath = AMP_CONTENT_URL_IMAGES . AMP_IMAGE_CLASS_OPTIMIZED . DIRECTORY_SEPARATOR;
        $linkpath = AMP_SITE_URL . AMP_CONTENT_URL_IMAGES . AMP_IMAGE_CLASS_ORIGINAL. DIRECTORY_SEPARATOR;
        if ( defined( 'AMP_USERMODE_ADMIN') && AMP_USERMODE_ADMIN ) {
            $linkpath = AMP_SITE_URL . 'system/' . AMP_SYSTEM_URL_IMAGES . '?action=add&file=';
        }

        $picker->updateAttributes( 
            array( 
                    'onChange' => 
                    "AMP_swapLoadImage( '$srcpath' + this.value, '$display_name' );" 
                    ."AMP_swapLinkTarget( '$linkpath' + this.value, '$display_name'+'_link');"
                    ."AMP_showValid( this.value, '$display_name'+'_container');",
                    'onKeyUp' => 
                    "AMP_swapLoadImage( '$srcpath' + this.value, '$display_name' );" 
                    ."AMP_swapLinkTarget( '$linkpath' + this.value, '$display_name'+'_link');"
                    ."AMP_showValid( this.value, '$display_name'+'_container');"
                    ));
        return $picker;

    }

    function &_addElementCheckBox( $name, $field_def ){
        $this->addTranslation( $name, '_returnBlankCheckbox', 'get' );
        return $this->_addElementDefault( $name, $field_def );
    }

    function _returnBlankCheckbox( $data, $fieldname ){
        if ( !isset( $data[$fieldname])) return false;
        return $data[$fieldname];
    }

    function &_addElementDate ( $name, $field_def ) {
        $defaults = array();
        $date_def = $this->getField( $name );
        if (isset($date_def[ 'options' ])) $defaults = $date_def['options'];
        return $this->form->addElement( $field_def['type'], $name, $field_def['label'], $defaults );
        
    }

    function &_addElementHtml( $name, $field_def ) {
        $name = $this->_readTemplateBlock( $this->_getDefault( $name ), $field_def );
        return $this->form->addElement( 'html', $name );
    }

    function &_addElementSelect( $name, $field_def ) {
        $valueset = $this->_getValueSet( $name );
		if ( is_array( $valueset ) ) {
            $valueset = $this->_selectAddNull( $valueset, $name );
        } else {
            $valueset = $this->_blankValueSet( $valueset, $name );
        }
        return $this->form->addElement( 'select', $name, $field_def['label'], $valueset);
    }

    function _blankValueSet( $valueset, $name ){
        return array( '' => AMP_TEXT_NONE_AVAILABLE );
    }

    function _selectAddNull( $valueset, $name ) {
        if (!is_array($valueset)) return false;
        return array('' => 'Select one') + $valueset;
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
        $label = $field_def['label'];
		if (strlen($defaults) > 0)  $label = $defaults;
        return $this->form->addElement( 'header', $name, $label);
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

    function &_addElementGroup( $name, $field_def ){
        if ( !isset( $field_def['elements'])) return false;
        $elementSet = $field_def['elements'];
        $formSet = array( );
        foreach( $elementSet as $element_id => $element_def ){
            $this->_addElement( $element_id, $this->_confirmFieldDef( $element_def ));
            $formSet[$element_id] = &$this->form->removeElement( $element_id );
        }
        $separator = $this->template->getPatternPart( 'newrow ') ;
        return $this->form->addGroup( $formSet, $name, $field_def['label'], $separator );
    }

    function &_addElementWysiwyg ( $name, $field_def ) {
        $field_def['type'] = 'textarea';
        $fRef = &$this->_addElementDefault( $name, $field_def );
        $fRef->updateAttributes ( array( "id"=>$name ) );
        $this->_adjustElementTextarea( $fRef, $field_def );

        //if ( isset( $_COOKIE['AMPWYSIWYG']) && 'none' == $_COOKIE['AMPWYSIWYG'] ) return $fRef;
        if ( !AMP_USER_CONFIG_USE_WYSIWYG ) return $fRef;
        if ( array_search( getBrowser( ), array( 'win/ie', 'mozilla' )) === FALSE) return $fRef; 
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

    function _adjustElementFile( &$fRef, $field_def ) {
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

        if ( isset( $def['lookup'] ) && $values = AMP_evalLookup( $def['lookup']) ) {
            $this->setFieldValueSet( $name, $values );
            return $values;
        }

        if (!( isset($def['values']) && $def['values'])) return null;  

        if ( !is_array( current( $def['values']))) return $def['values'];

        $option_array = array( );
        foreach( $def['values'] as $v_key => $value ) {
            if ( !isset( $value['key']) || !isset( $value['value'])) continue; 
            $option_array[ $value['key']] = $value[ 'value' ];
        }
        return $option_array;
    }
/*** moved to utility.function AMP_evalLookup 2006-03-01 AP
    function _evalLookup( $lookup_def ){
        if ( !is_array( $lookup_def )) return AMPSystem_Lookup::instance( $lookup_def );
        if ( isset( $lookup_def['module'])){
            return AMPSystem_Lookup::locate( $lookup_def );
        }
        return array( );
    }
    */

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
		$newdef['block']    = (isset($field_def['block']))   ? $field_def['block']       : null;
		$newdef['block_trigger']    = (isset($field_def['block_trigger']))   ? $field_def['block_trigger']       : null;
		$newdef['rules']     = (isset($field_def['rules']))   ? $field_def['rules']       : null;

        $no_template_set = array( 'html', 'blocktrigger');
        if ( array_search( $newdef['type'], $no_template_set)!==FALSE) return $newdef + array( 'template' => null );   

        $separator = isset($field_def['separator'])? $field_def['separator'] : "";
        $newdef['template'] = (isset($field_def['template'])? $field_def['template']: $this->_getTemplate( $newdef['type'], $separator ));

        $newdef['template'] = $this->_readTemplateBlock( $newdef['template'], $field_def );

        return $newdef;
    }

    function _readTemplateBlock( $template, $field_def ) {
        //no block set
        if ( 'hidden' == $field_def['type']) return $template;
        if ( !( isset( $field_def['block']) && $field_def['block'] )){

            //none requested
            if ( !$current_block = $this->_current_block ) return $template;
            
            //current block ends
            $this->_current_block = "";
            return $this->template->endBlock( $current_block, $template );
        }
        //current block is continued
        if ( $field_def['block'] == $this->_current_block) return $template;

        //start a new block
        if ( !$block = $this->_current_block ) {
            $this->_current_block = $field_def['block'];
            if ( $field_def['type'] == 'blocktrigger') {
                return $this->template->triggerBlock( $field_def['block'], $template);
            }
            return $this->template->startBlock( $field_def['block'], $template );
        }
        //start a new block and end the last one
        $this->_current_block = $field_def['block'];
        if ( $field_def['type'] == 'blocktrigger') {
            return $this->template->endBlock( $block,$this->template->triggerBlock( $field_def['block'], $template));
        }
        return $this->template->endBlock( $block, $this->template->startBlock( $field_def['block'], $template));

    }

    function _verifyConditionals( $field_def ) {
        if (!isset( $this->conditionals )) return true;
        
        foreach ($this->conditionals as $key => $requirement ) {
            if (!isset( $field_def[ $key ] )) return false;
            if ( $field_def [ $key ] != $requirement ) return false;
        }

        if ( isset( $field_def['per'])) {
            $this->_loadPermissionManager( );
            $per = $this->_per_manager->convertDescriptor( $field_def['per']);
            return AMP_Authorized( $per );
        }

        return true;
    }

    function &_loadPermissionManager( ){
        if ( isset( $this->_per_manager)) return true;

        require_once( 'AMP/System/Permission/Manager.inc.php');
        $this->_per_manager = &AMPSystem_PermissionManager::instance();
    }

    function removeSubmit( $submit_value ){
        //interface
    }

    function postSave( $data ){
        //interface
    }

    function &_get_map( ) {
        if ( isset( $this->_map )) return $this->_map;
        require_once( 'AMP/System/ComponentLookup.inc.php');
        $this->_map = &ComponentLookup::instance( get_class( $this ));
        return $this->_map;
    }

    function _allowedFields( $fields ) {
        //interface, no action taken
        return $fields;
    }

    function __sleep( ) {
        $values = get_object_vars( $this );
        unset( $values['_map']);
        return array_keys( $values );
    }

    function __wakeup( ) {
        $this->_after_init( );
        $this->_registerCustomElementTypes( );
        $this->_awakenGroups( );
        $this->_awakenJS( );
    }

    function _awakenGroups( ) {
        $group_types = array( 'group', 'checkgroup', 'radiogroup', 'date');
        foreach( $this->fields as $name => $field_def ) {
            if ( array_search( $field_def['type'], $group_types ) !== FALSE ) {
                $this->addField( $field_def, $name);
            }
        }
    }

    function _awakenJS( ) {
        if ( !AMP_USER_CONFIG_USE_WYSIWYG ) return;
        if ( array_search( getBrowser( ), array( 'win/ie', 'mozilla' )) === FALSE) return; 

        $js_types = array( 'wysiwyg');
        foreach( $this->fields as $name => $field_def ) {
            if ( array_search( $field_def['type'], $js_types ) !== FALSE ) {
                if ( !isset( $editor )) {
                    $editor = &AMPFormElement_HTMLEditor::instance();
                }

                $editor->addEditor( $name );
            }
        }
    }

 }
 ?>
