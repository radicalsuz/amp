<?php

class ElementCopierScript {

    var $fields;
    var $formname_default;
    var $start_qty_default =0;
    var $copier_name_default = "copymaker";
    var $copiers = array();

    function ElementCopierScript( $fields = null ) {
        $this->init($fields);
    }

    function init($fields = null) {
        if (isset($fields)) $this->addCopier( $this->copier_name_default, $fields ); 
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

    function setFields( $copiername, $fieldset ) {
        $this->copiers[$copiername] = array();
        $this->setCoreField( $copiername, key( $fieldset ) );
        foreach ( $fieldset as $fieldname => $def ) {
            $this->addField( $def, $fieldname, $copiername );
        }
        $this->addControlButtons( $copiername );
    }

    function addControlButtons( $copiername ) {
				/*
        $add_button =
            array( 'type' => 'button', 
                   'action' => 'DuplicateElementSet( window.' . $copiername . " );",
                   'label' => '+' );
									 */
									 
        $del_button =
            array( 'type' => 'button',
                   'action' => 'window.' . $copiername . ".RemoveCurrentSet( this );",
                   'label' => 'Remove' );
        #$this->addField( $add_button, 'add_btn', $copiername );
        $this->addField( $del_button, 'form_del_btn', $copiername );
    }

    function addField( $field_def, $fieldname, $copiername ) {
        $this->copiers[$copiername]['fields'][$fieldname] = $field_def;
    }


    function script_header() {
        $script ='
            <script type="text/javascript" src = "/scripts/elementCopier.js"></script>';
        $script .= '<script type="text/javascript">
            function loadCopier() {'."\n";

        foreach ($this->copiers as $copiername => $copierDef ) {
            $fieldset = $copierDef[ 'fields' ];
            $script.= 'window.'.$copiername.' = new ElementCopier("'.$copierDef['formname'].'", '.$copierDef['start_qty'].');'."\n";
            $script.= $this->_js_initCopier( $fieldset, $copiername );
            $script.= $this->_js_outputSets( $copiername );
        }
        $script .= "}\n loadCopier();\n </script>";

        return $script;
    }

    function script_value_array( $valuevar, $fDef ) {
        $script = "var $valuevar = new Array();\n var valuecounter=0;\n";
        $script .= $valuevar . "[ valuecounter++] = new Option(\"Add New\",'');\n";

        foreach ($fDef['values'] as $key => $value ) {
            $script .= $valuevar . "[ valuecounter++] = new Option(\"". str_replace( "&nbsp;", " ", $value) . "\",'". $key . "');\n";
        }

        return $script;
    }

    function _js_initCopier( $fieldset, $copiername ) {
        $script = "";
        foreach ($fieldset as $fieldname => $fDef ) {
            $valuevar = '""';
            if (isset($fDef['default']) && $fDef['default']) $valuevar = $this->_delimit( $fDef['default'] );
            if (isset($fDef['values']) && $fDef['values']) {
                if (is_array( $fDef['values'] )) {
                    $valuevar = $copiername.'_'.$fieldname.'_values';
                    $script .= $this->script_value_array( $valuevar, $fDef ); 
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

        return $script;
    }

    function _addPrefix( $copier, $fieldname ) {
        if (!isset($this->copiers[ $copier ][ 'prefix' ])) return $copier.'_'.$fieldname;
        return $this->copiers[ $copier ][ 'prefix' ] . '_' . $fieldname;
    }

    function _delimit( $text ) {
        if (strpos( $text, "'" ) !== FALSE ) return '"'. $text . '"';
        return "'". $text . "'";
    }

    function getAddButton ($copier_name ) {
            return array( $this->_addPrefix( $copier_name, 'add_'.$copier_name ) =>
                array( 'type' => 'button',
                            'attr' => array( 'onClick' => 'DuplicateElementSet( window.'.$copier_name .', parentRow( this ).rowIndex );' ),
                            'label' => 'Add New Item',
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
        return $this->copiers[ $copier ][ 'valuesets' ];
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
            $script .= $namevar . "[ valuecounter++] = " . $this->_delimit( $this->_addPrefix( $copier_name, $key) ) . "\n";
        }
        return $script;
    }

    function _js_outputValueSet( $valuevar, $data ) {
        $script = "var $valuevar = new Array();\n var valuecounter=0;\n";
        foreach ( $data as $key => $value ) {
            if ( is_numeric( $key )) continue;
            $script .= $valuevar . "[ valuecounter++] = " . $this->_delimit( $this->_js_cleanvalue( $value ) ) . "\n";
        }
        return $script;
    }

    function _js_cleanValue( $value ) {
        return str_replace( "\r\n", "\\n", $value);
    }




}
?>
