<?php

class ElementCopierScript {

    var $fields;
    var $formname_default;
    var $start_qty_default =0;
    var $copier_name_default = "copymaker";
    var $copiers = array();
    var $_header;

    function ElementCopierScript( $fields = null ) {
        $this->init($fields);
    }

    function init($fields = null) {
		if(!defined('AMP_TEXT_FORM_ELEMENT_COPIER_ADD_BUTTON')) {
			define('AMP_TEXT_FORM_ELEMENT_COPIER_ADD_BUTTON', 'Add New Item');
		}
		if(!defined('AMP_TEXT_FORM_ELEMENT_COPIER_VALUE_ARRAY_DEFAULT')) {
			define('AMP_TEXT_FORM_ELEMENT_COPIER_VALUE_ARRAY_DEFAULT', 'Select One');
		}
		if(!defined('AMP_TEXT_FORM_ELEMENT_COPIER_REMOVE_BUTTON')) {
			define('AMP_TEXT_FORM_ELEMENT_COPIER_REMOVE_BUTTON', 'Remove');
		}

        if (isset($fields)) $this->addCopier( $this->copier_name_default, $fields ); 
        $this->_header = &AMP_getHeader( );
    }

    function addCopier( $name, $fieldset, $formname = null, $start_qty = null ) {
        if (!isset($formname)) $formname = $this->formname_default;
        if (!isset($start_qty)) $start_qty = $this->start_qty_default;
        $this->setFields( $name, $fieldset );
        $this->setFormName( $name, $formname );
        $this->setQuantity( $name, $start_qty );
    }

    function setQuantity( $copier, $qty ) {
        $this->copiers[ $copier ]['start_qty'] = $qty;
    }

    function setPrefix( $copier, $prefix ) {
        $this->copiers[ $copier ]['prefix'] = $prefix;
    }

    function getPrefix( $copier ) {
        return $this->copiers[ $copier ]['prefix'];
    }

    function setFormName( $copier, $formname ) {
        $this->copiers[ $copier ]['formname'] = $formname;
    }

    function &instance() {
        static $copier_script = false;
        if (!$copier_script) $copier_script = new ElementCopierScript();
        return $copier_script;
    }

    function setCoreField( $copiername , $fieldname ) {
        $this->copiers[ $copiername ]['core_field'] = $fieldname;
    }

    function setFields( $copiername, $fieldset, $add_controls = true ) {
        $this->copiers[$copiername] = array();
        $this->setCoreField( $copiername, key( $fieldset ) );
        foreach ( $fieldset as $fieldname => $def ) {
            $this->addField( $def, $fieldname, $copiername );
        }
        if ( $add_controls ) $this->addControlButtons( $copiername );
		if( defined('AMP_FORM_ELEMENT_COPIER_DELIMITER') ) {
			$delimiter = array( 'type' => 'static',
							    'values' => AMP_FORM_ELEMENT_COPIER_DELIMITER);
			$this->addField( $delimiter, 'delimiter', $copiername );
		}
    }

    function addControlButtons( $copiername ) {
									 
        $del_button =
            array( 'type'   => 'button',
                   'action' => 'window.' . $copiername . ".RemoveCurrentSet( this );",
                   'label'  => $this->getButtonText( 'remove', $copiername ) );

        $this->addField( $del_button, 'form_del_btn', $copiername );

    }

    function getButtonText( $button, $copiername ) {
        if ( !( isset( $this->copiers[$copiername]['text'] ) && isset( $this->copiers[$copiername]['text'][$button]))) {
            return constant( 'AMP_TEXT_FORM_ELEMENT_COPIER_' . strtoupper( $button ) . '_BUTTON');
        }
        return $this->copiers[$copiername]['text'][$button];
    }

    function setButtonText( $buttontext, $button, $copiername) {
        $this->copiers[$copiername]['text'][$button] = $buttontext; 
    }

    function addField( $field_def, $fieldname, $copiername ) {
        $this->copiers[$copiername]['fields'][$fieldname] = $field_def;
    }


    function script_header() {
        //$script ='<script type="text/javascript" src = "/scripts/elementCopier.js"></script>';
        $this->_header->addJavaScript( 'scripts/elementCopier.js', 'copier_base');
        $script = 
        'function loadCopier() {'."\n";

        foreach ($this->copiers as $copiername => $copierDef ) {
            $fieldset = $copierDef[ 'fields' ];
            $script.= 'window.'.$copiername.' = new ElementCopier("'.$copierDef['formname'].'", '.$copierDef['start_qty'].');'."\n";
            $script.= $this->_js_initCopier( $fieldset, $copiername );
            $script.= $this->_js_outputSets( $copiername );
        }
        $script .= "}\n";
        $this->_header->addJavascriptDynamic( $script, 'copier_dynamic' );
        $this->_header->addJavascriptOnLoad( "loadCopier();", 'copier_init' );

        return false;
    }

    function script_value_array( $valuevar, $fDef, $null_value = AMP_TEXT_FORM_ELEMENT_COPIER_VALUE_ARRAY_DEFAULT ) {
        $script = "var $valuevar = new Array();\n var valuecounter=0;\n";
        if ( $null_value ) $script .= $valuevar . "[ valuecounter++] = new Option(\"". $null_value ."\",'');\n";
        

        foreach ($fDef['values'] as $key => $value ) {
            $script .= $valuevar . "[ valuecounter++] = new Option(\"". str_replace( "&nbsp;", " ", $value) . "\",'". $key . "');\n";
        }

        return $script;
    }

    function _getNullSelectOption( $fieldname, $copiername ) {
        if (!( isset( $this->copiers[$copiername]['fields'][$fieldname]) 
            && isset( $this->copiers[$copiername]['fields'][$fieldname]['default_option'])  )) {
            return AMP_TEXT_FORM_ELEMENT_COPIER_VALUE_ARRAY_DEFAULT ;
        }
        
        return $this->copiers[ $copiername ][ 'fields' ][ $fieldname ][ 'default_option' ]  ;
    }

    function setNullSelectOption(  $null_value, $fieldname, $copiername ) {
        $this->copiers[ $copiername ][ 'fields' ][ $fieldname ][ 'default_option' ] = $null_value;
    }

    function setSingleRow( $singleRow = true, $copiername ){
        $this->copiers[ $copiername ]['singleRow'] = $singleRow;
    }

    function setLabelColumn( $label_column = true, $copiername ){
        $this->copiers[ $copiername ]['label_column'] = $label_column;
    }

    function setFormTable( $table_id, $copiername ){
        $this->copiers[ $copiername ]['table_id'] = $table_id;
    }

    function setRowOffset( $offset_qty, $copiername ){
        $this->copiers[ $copiername ]['offset_qty'] = $offset_qty;
    }

    function setElementClass( $classname, $copiername ){
        $this->copiers[ $copiername ]['css_class_elements'] = $classname;
    }

    function _js_initCopier( $fieldset, $copiername ) {
        $script = "";
        foreach ($fieldset as $fieldname => $fDef ) {
            $valuevar = '""';
            if (isset($fDef['default']) && $fDef['default']) $valuevar = $this->_delimit( $fDef['default'] );
            if (isset($fDef['values']) && $fDef['values']) {
                if (is_array( $fDef['values'] )) {
                    $valuevar = $copiername.'_'.$fieldname.'_values';
                    $script .= $this->script_value_array( $valuevar, $fDef, $this->_getNullSelectOption( $fieldname, $copiername ) ); 
                } else {
                    $valuevar = $this->_delimit( $fDef['values'] );
                }
            } 
            $actionvar = (isset($fDef['action']) && $fDef['action']) ? $this->_delimit( $fDef['action'] ) : "''";
						$label =  (isset($fDef['label']) && $fDef['label']) ? $this->_delimit( $fDef['label'] ) : "''";
						$fieldname = $this->_delimit( $this->_addPrefix( $copiername , $fieldname ) );
						$type = $this->_delimit( $fDef['type'] );

            $script .= "$copiername.defineElement( $fieldname, $type, $label, $valuevar, $actionvar );\n"; 
        }
        $script .= $this->_js_configCopier( $copiername );

        return $script;
    }

    function _js_configCopier( $copiername ){
        $config_script = "";
        if ( isset( $this->copiers[ $copiername ]['singleRow']) && $this->copiers[ $copiername ]['singleRow'] ) {
            $config_script .= "$copiername.singleRow = true;\n"; 
        }
        if ( isset( $this->copiers[ $copiername ]['label_column']) && !$this->copiers[ $copiername ]['label_column'] ) {
            $config_script .= "$copiername.labelColumn = false;\n"; 
        }
        if ( isset( $this->copiers[ $copiername ]['table_id']) && $this->copiers[ $copiername ]['table_id'] ) {
            $config_script .= "$copiername.setFormTable( '".$this->copiers[ $copiername ][ 'table_id' ]."' );\n"; 
        }
        if ( isset( $this->copiers[ $copiername ]['offset_qty']) ) {
            $config_script .= "$copiername.startRowOffset = ".$this->copiers[ $copiername ][ 'offset_qty' ].";\n"; 
        }
        if ( isset( $this->copiers[ $copiername ]['css_class_elements']) && $this->copiers[ $copiername ]['css_class_elements'] ) {
            $config_script .= "$copiername.cssElementClassName = '".$this->copiers[ $copiername ][ 'css_class_elements' ]."';\n"; 
        }
        return $config_script;

    }

    function _addPrefix( $copier, $fieldname ) {
        if (!isset($this->copiers[ $copier ][ 'prefix' ])) return $copier.'_'.$fieldname;
        return $this->copiers[ $copier ][ 'prefix' ] . '_' . $fieldname;
    }

    function _delimit( $text ) {
//        if ( (strpos( $text, "'" ) !== FALSE ) || (strpos( $text, "\\" ) !== FALSE ) ) {
        if (strpos( $text, "'" ) !== FALSE ) {
			return '"'. $text . '"';
		}
        return "'". $text . "'";
    }

    function getAddButton ($copier_name ) {
            return array( $this->_addPrefix( $copier_name, 'add_'.$copier_name ) =>
                array( 'type' => 'button',
                            'attr' => array( 'onClick' => 'DuplicateElementSet( window.'.$copier_name .', parentRow( this ).rowIndex );' ),
                            'label' => $this->getButtonText( 'add', $copier_name ),
                            'public' => true,
                            'enabled' => true ) );
    }

    function output( ) {
        return $this->script_header();
    }

    function validateSets( $data ) {
        $result_set = array();
        foreach ($this->copiers as $copier_name => $copierDef ) {
            $validation_mark = 'AMP_elementCopier_' . $copier_name . '_validated';
            if (!is_array($data[ $validation_mark ])) continue;

            foreach ($data[ $validation_mark ] as $key => $valid) {
                if ($valid) $result_set[] = $key;
            }
        }

        return $result_set;
    }

    function getValidationField( $copier_name ) {
        return array( 'AMP_elementCopier_'.$copier_name.'_validated' =>
            array( 		'type' => 'hidden',
                                'public' => true,
                                'enabled' => true ) );
    }

        
/*
    function parseCopiedElements( $copier, $all_data ) {
        //if (!($valid_element_sets = $this->validateSets( $all_data ))) return false;
        $result_set = array();
				$set_type =  &$all_data[ $this->copiers[ $copier ][ 'core_field' ] ];
        foreach ($set_type as $set_id => $set_values ) {
            $result_set[] = $this->parseSingleSet( $set_id, $all_data );
        }

        return $result_set;
    }

    function parseSingleSet( $set_id, &$all_data ) {
        $result_set = array();
        foreach( $this->fields as $fieldname => $fielddef ) {
            if (!isset($all_data[ $fieldname ][ $set_id ])) continue;
            $result_set[ $fieldname ] = $all_data[ $fieldname] [$set_id];
        }
        return $result_set;
    }
    */

    function makeSets( $copier, $data ) {
        $sets = array();
        $core_field = $this->_addPrefix( $copier, $this->copiers[ $copier ][ 'core_field' ] );
        if (!isset( $data[ $core_field ])) return false;

        foreach( $data[ $core_field ] as $set_index => $data_item ) {
            if ( !$data_item ) continue;
            foreach( $this->copiers[ $copier]['fields'] as $fieldName => $fieldDef ) {
                if ( !isset( $data[ $this->_addPrefix( $copier, $fieldName) ] )) continue;
                $sets [ $set_index ][ $fieldName ] = $data[ $this->_addPrefix( $copier, $fieldName ) ][ $set_index ] ;
            }
        }
        return $sets;
    }


    function addSets( $copier, $data ) {
        $real_sets = $this->makeSets( $copier, $data );
        if (empty( $real_sets )) return false;

        $this->copiers[ $copier ][ 'valuesets' ] = $real_sets;
    }

    function addRealSets( $copier, $data ){
        if ( !isset( $data[ $copier ])) return false;
        if ( empty( $data[ $copier ])) return false;
        $this->copiers[ $copier ][ 'valuesets' ] = $data[ $copier ];
    }

    function returnSets( $copier ) {
        if (!isset( $this->copiers[ $copier ][ 'valuesets' ])) return false;
		$sets = $this->_js_uncleanValueSet($this->copiers[$copier]['valuesets']);		
        return $sets;
    }
  
    function _js_outputSets( $copier_name ) {
        $copierDef = $this->copiers[ $copier_name ];
        if (!isset( $copierDef['valuesets'])) return false;
        $script = "";
        
        $valuevar = $copier_name . '_activeValues';
        $namevar = $copier_name . '_activeNames';

        $script .= $this->_js_outputNameSet( $namevar, current( $copierDef['valuesets'] ), $copier_name ) . "\n";
        $add_button_name = key( $this->getAddButton( $copier_name ));
              
        foreach( $copierDef['valuesets'] as $valueSet ) {
            $script .= $this->_js_outputValueSet( $valuevar, $valueSet ) . "\n";
            $script .= 'restoreSet( window.' . $copier_name . ', ' . 
                                    $this->_delimit( $add_button_name ) . ', ' .
                                    $valuevar . ', ' .
                                    $namevar . " );\n\n\n ";
        }
                

        return $script;
    }
    function _js_outputNameSet( $namevar, $data, $copier_name ) {
        $script = "var $namevar = new Array();\n var valuecounter=0;\n";
        foreach ( $data as $key => $value ) {
            if ( is_numeric( $key )) continue;
            $script .= $namevar . "[ valuecounter++] = " . $this->_delimit( $this->_addPrefix( $copier_name, $key) ) . ";\n";
        }
        return $script;
    }

    function _js_outputValueSet( $valuevar, $data ) {
        $script = "var $valuevar = new Array();\n var valuecounter=0;\n";
        foreach ( $data as $key => $value ) {
            if ( is_numeric( $key )) continue;
            $script .= $valuevar . "[ valuecounter++] = '" . $this->_js_cleanvalue( $value ) . "';\n";
        }
        return $script;
    }

    function _js_cleanValue( $value ) {
        $base_values = array( "\r\n", "'");
        $replace_values = array( "\\n" , "\'");
        return str_replace( $base_values, $replace_values,  $value );
    }

	function _js_uncleanValueSet($value) {
		$value = is_array($value) ?
               array_map(array($this, '_js_uncleanValueSet'), $value) :
               str_replace("\'", "'", $value);

		return $value;
	}




}
?>
