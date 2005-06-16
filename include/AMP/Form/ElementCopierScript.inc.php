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

}
