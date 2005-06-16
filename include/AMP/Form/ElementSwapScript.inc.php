<?php
define ('AMP_FORM_ELEMENTSWAP_DEFAULT_SET', 'default');

class ElementSwapScript {

    var $sets;
    var $fields;
    var $swapper_name = "amp_elementswap";
    var $formname;
    var $initial_set = "";


    function ElementSwapScript ( $name = null, $fields = null ) {
        $this->init( $name, $fields );
    }

    function init( $name= null, $fields=null) {
        if (isset($name)) $this->swapper_name = $name;
        if (isset($fields)) $this->sets[AMP_FORM_ELEMENTSWAP_DEFAULT_SET] = array_keys($fields);
    }

    function addSet ($setname, $fields) {
        $this->sets[$setname] = $fields;
    }

    function script_header() {
        $script = '<script type="text/javascript" src = "/scripts/elementSwapper.js"></script>'."\n".
                    '<script type = "text/javascript">
                    function loadSwapper_'. $this->swapper_name .'() {
                        window.'.$this->swapper_name.' = new ElementSwapper("'.$this->formname."\");\n".
                        $this->swapper_name.".visibleStyle = \"".(getBrowser() == "win/ie"?"inline":"table-row")."\";\n";

        foreach ($this->sets as $setkey => $fieldset ) {
            $script .= $this->swapper_name.".addSwapSet( '$setkey' );\n";

            foreach ($this->sets[$setkey] as $fieldkey => $fDef ) {
                if (is_string($fDef)) $fieldname = $fDef;
                if (!is_numeric($fieldkey)) $fieldname = $fieldkey;
                $script .= $this->swapper_name.".addSwapElement( '$fieldname', '$setkey' );\n";
            }
        }

        $script .= "} \nloadSwapper_".$this->swapper_name."();\n".
                    "ActivateSwap( window.".$this->swapper_name.", '".$this->initial_set."');\n</script>";

        return $script;
    }

    function output( ) {
        return $this->script_header();
    }

}
?>
