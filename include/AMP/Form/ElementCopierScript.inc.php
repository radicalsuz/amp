<?php

class ElementCopierScript {

    var $fields;
    var $formname;
    var $start_qty=0;
    var $copier_name = "copymaker";
    var $copiers = array();

    function ElementCopierScript( $fields = null ) {
        $this->init($fields);
    }

    function init($fields = null) {
        if (isset($fields)) $this->addCopier( $this->copier_name, $fields ); 
    }

    function addCopier( $name, $fieldset ) {
        $this->setFields( $name, $fieldset );
    }

    function setFields( $copiername, $fieldset ) {
        $this->copiers[$copiername] = array();
        foreach ( $fieldset as $fieldname => $def ) {
            $this->addField( $def, $fieldname, $copiername );
        }
        $this->addControlButtons( $copiername );
    }

    function addControlButtons( $copiername ) {
        $add_button =
            array( 'type' => 'button', 
                   'action' => 'DuplicateElementSet( window.' . $copiername . " );",
                   'label' => '+' );
        $del_button =
            array( 'type' => 'button',
                   'action' => 'window.' . $copiername . ".RemoveSet( this.id.substring(14) );",
                   'label' => '-' );
        $this->addField( $add_button, 'add_btn', $copiername );
        $this->addField( $del_button, 'del_btn', $copiername );
    }

    function addField( $field_def, $fieldname, $copiername ) {
        $this->copiers[$copiername][$fieldname] = $field_def;
    }


    function script_header() {
        $script ='
            <script type="text/javascript" src = "/scripts/elementCopier.js"></script>';
        $script .= '<script type="text/javascript">
            function loadCopier() {'."\n";

        foreach ($this->copiers as $copiername => $fieldset) {
            $script.= 'window.'.$copiername.' = new ElementCopier("'.$this->formname.'", '.$this->start_qty.');'."\n";
            $script.= $this->_js_initCopier( $fieldset, $copiername );
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
            if (isset($fDef['values']) && $fDef['values']) {
                $valuevar = $copiername.'_'.$fieldname.'_values';
                $script .= $this->script_value_array( $valuevar, $fDef ); 
            }
            $actionvar = '""';
            if (isset($fDef['action']) && $fDef['action']) $actionvar = "'".$fDef['action']."'";

            $script .= $copiername.".defineElement( '$fieldname', '".$fDef['type']."', '".$fDef['label']."', $valuevar, $actionvar);\n"; 
        }

        return $script;
    }




    function output( ) {
        return $this->script_header();
    }

    function validateSets( $data ) {
        $validation_mark = 'AMP_elementCopier_' . $this->copier_name . '_validated';
        if (!is_array($data[ $validation_mark ])) return false;

        $result_set = array();
        foreach ($data[ $validation_mark ] as $key => $valid) {
            if ($valid) $result_set[] = $key;
        }

        return $result_set;
    }

        

    function parseCopiedElements( $all_data ) {
        if (!($valid_element_sets = $this->validateSets( $all_data ))) return false;
        $result_set = array();
        foreach ($this->valid_element_sets as $set_id) {
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

}
?>
