<?php
define ('AMP_FORM_ELEMENTSWAP_DEFAULT_SET', 'default');

class ElementSwapScript {

    var $swappers=array();
    var $formnames;
    var $initial_set = "";
    var $_header;


    function ElementSwapScript ( ) {
        $this->init();
    }

    function init() {
        //interface
        $this->_header = &AMP_getHeader( );
    }

    function &instance() {
        static $swapper = false;
        if (!$swapper) $swapper = new ElementSwapScript();
        return $swapper;
    }

    function addSwapper( $name ) {
        if (isset($this->swappers[ $name ])) return false;
        $this->swappers[ $name ] = array();
    }

    function setForm( $formname, $swapper ) {
        $this->formnames[ $swapper ] = $formname;
    }

    function getFormName( $swapper ) {
        if (isset($this->formnames[ $swapper ])) return $this->formnames[ $swapper ];
        return false;
    }

    function addSet ($setname, $fields, $swapper_name  ) {
        $this->swappers[ $swapper_name ][$setname ] = $fields;
    }

    function script_header() {
        $this->_header->addJavaScript( '/scripts/elementSwapper.js', 'element_swap_library');
        #$script = '<script type="text/javascript" src = "/scripts/elementSwapper.js"></script>'."\n";
        #$script .= '<script type = "text/javascript">'."\n";
        $script = '';
         
        foreach ($this->swappers as $swapper_name => $swapper_set ) {
            $script .= 'function loadSwapper_'. $swapper_name .'() {'. "\n".
                       'window.'.$swapper_name.' = new ElementSwapper("'.$this->getFormName( $swapper_name )."\");\n".
                        $swapper_name.".visibleStyle = \"".(getBrowser() == "win/ie"?"inline":"table-row")."\";\n";

            foreach ($swapper_set as $setkey => $fieldset ) {
                $script .= $swapper_name.".addSwapSet( '$setkey' );\n";

                foreach ($fieldset as $fieldkey => $fDef ) {
                    if (is_string($fDef)) $fieldname = $fDef;
                    if (!is_numeric($fieldkey)) $fieldname = $fieldkey;
                    $script .= $swapper_name.".addSwapElement( '$fieldname', '$setkey' );\n";
                }
            }
            $script .=  $this->js_ActivateInitial( $swapper_name ).
                        "} \n";
            $this->_header->addJavascriptOnLoad( "loadSwapper_".$swapper_name."();");
        }
        #$script .= "</script>";
        $this->_header->addJavascriptDynamic( $script, 'element_swap_dynamic' );

        return false;
    }

    function js_ActivateInitial( $swapper_name ) {
            return  "ActivateSwap( window.".$swapper_name.", '".$this->getInitialValue( $swapper_name )."');\n";
    }

    function getInitialValue( $swapper_name ) {
        if (!isset($this->initial_sets[ $swapper_name ] )) return false;
        return $this->initial_sets[ $swapper_name ];
    }
    function setInitialValue( $set_id, $swapper_name ) {
        $this->initial_sets[ $swapper_name ] = $set_id ;
    }

    function js_swapAction( $swapper_name ) {
        return 'ActivateSwap( window.'. $swapper_name .', this.value );';
    }

    function output( ) {
        return $this->script_header();
    }

}
?>
