<?php

class AMPFormElement_HTMLEditor {

    var $editors = array();
    var $plugins = array(
            "'SpellChecker'",
            "'FullScreen'",
            "'FindReplace'",
            "'SuperClean'",
            );
    var $config_location = "/scripts/xinha_config.js";
    var $width = '550px';
    var $height = '420px';
    var $stylesheet = '/custom/styles.css';
    var $config_actions = array();

    function AMPFormElement_HTMLEditor() {
    }

    ################################
    ###  Public Setup Functions  ###
    ################################


    function &instance() {
        static $editor = false;
        if (!$editor) {
            $editor = new AMPFormElement_HTMLEditor();
        }
        return $editor;
    }

    function addEditor( $elementname ) {
        $elementname = $this->_delimit( $elementname );
        if (array_search($elementname, $this->editors)!==FALSE) return false;
        $this->editors[] = $elementname;
    }


    function output() {
        if (empty($this->editors)) return false;

        return $this->_script_header(). $this->_config_script();
    }

    function register_config_action( $action ) {
        $this->config_actions[] = $action;
    }

    ##################################
    ###  Private Script Functions  ###
    ##################################

    function _script_header() {
          
        return
        '<script type="text/javascript">
            _editor_url  = "/scripts/xinha/" 
            _editor_lang = "en";      // And the language we need to use in the editor.
        </script>
        <script type="text/javascript" src="/scripts/xinha/htmlarea.js"></script>'."\n";
    }

    function _config_script() {
        $plugins = count( $this->plugins )?"\n[\n". join(",\n", $this->plugins)."\n]":"null";

        $output  = "<script type=\"text/javascript\">\n";
        $output .= "xinha_editors = \n[\n". join(",\n", $this->editors)."\n];\n";
        $output .= "xinha_plugins = $plugins;\n";
        $output .= $this->_xinha_config();

        $output .= "</script>\n";

        $output .= "<script type=\"text/javascript\" src=\"".$this->config_location."\"></script>\n";
        return $output;
    }

    function _xinha_config() {
        $output = "function xinha_config(){ \n";
        $output .= "    cfg = new HTMLArea.Config();\n";
        $output .= "    cfg.width  = '".$this->width."';\n";
        $output .= "    cfg.height = '".$this->height."';\n";
        if ($this->isPlugin('Stylist')) {
            $output .= "    cfg.stylistLoadStylesheet('".$this->stylesheet."');\n";
        }
        if (!empty($this->config_actions)) {
            foreach ($this->config_actions as $action) {
                $output.= $action."\n";
            }
        }
        $output .= "    return cfg; \n } \n";

        return $output;
    }


    #################################
    ###  Public Plugin Functions  ###
    #################################

    function addPlugin( $plugin_name ) {
        if ($this->isPlugin( $plugin_name )) return false;
        $plugin_name = $this->_delimit($plugin_name);
        $this->plugins[] = $plugin_name;
    }

    function isPlugin( $plugin_name ) {
        $plugin_name = $this->_delimit($plugin_name);
        if (array_search($plugin_name, $this->plugins)===FALSE) return false;
        return true;
    }

    function removePlugin( $plugin_name ) {
        $key = array_search($this->_delimit( $plugin_name ), $this->plugins);
        if ($key === FALSE) return false;
        unset ($this->plugins[$key] );
        return true;
    }

    function _delimit( $item_name ) {
        return "'". $item_name ."'";
    }

}
?>
