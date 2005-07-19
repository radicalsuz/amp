<?php

class ElementCopierScript {

    var $fields;
    var $formname;
    var $start_qty=0;
    var $copier_name = "copymaker";

    function ElementCopierScript( $fields ) {
        $this->init($fields);
    }

    function init($fields) {
        $this->fields = $fields;
    }


    function script_header() {
        $script ='
            <script type="text/javascript" src = "/scripts/elementCopier.js"></script>';
        $script .= '<script type="text/javascript">
            function loadCopier() {
            window.'.$this->copier_name.' = new ElementCopier("'.$this->formname.'", '.$this->start_qty.');'."\n";

        foreach ($this->fields as $fieldname => $fDef ) {
            $script .= $this->copier_name.".defineElement( '$fieldname', '".$fDef['type']."', '".$fDef['label']."');\n"; 
        }

        $script .= "}\n loadCopier();\n </script>";


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
