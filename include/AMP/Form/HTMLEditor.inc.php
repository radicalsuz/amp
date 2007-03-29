<?php

class AMPFormElement_HTMLEditor {

    var $editors = array();
    var $plugins = array(
            "'SpellChecker'",
            "'FullScreen'",
            "'FindReplace'",
            "'Stylist'",
			 "'TableOperations'",
            "'SuperClean'",
            );
    var $config_location = "/scripts/xinha_config.js";
    var $width = 550;
    var $height = 420;
    var $stylesheet = '/custom/wysiwyg_styles.css';
    var $config_actions = array();
    var $_header;

    function AMPFormElement_HTMLEditor() {
        $this->_header = & AMP_getHeader( );
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
        $this->_header->addJavascriptOnLoad( 'xinha_init();', 'editor_init' );

        $this->_script_header(); 
        $this->_config_script();
        return false;
    }

    function register_config_action( $action ) {
        $this->config_actions[] = $action;
    }

    ##################################
    ###  Private Script Functions  ###
    ##################################

    function _script_header() {
        $this->_header->addJavascriptDynamicPrefix( 
            '_editor_url  = "/scripts/xinha/";' . "\n"
            . '_editor_lang = "en";      // And the language we need to use in the editor.',
            'editor_options');
        $this->_header->addJavascript( 'scripts/xinha/htmlarea.js', 'htmlarea' );
        #return '<script type="text/javascript" src="/scripts/xinha/htmlarea.js"></script>'."\n";
        return false;
          
          /*
        return
        '<script type="text/javascript">
            _editor_url  = "/scripts/xinha/" 
            _editor_lang = "en";      // And the language we need to use in the editor.
        </script>
        <script type="text/javascript" src="/scripts/xinha/htmlarea.js"></script>'."\n";
        */
    }

    function _config_script() {
        if ($this->isPlugin('Stylist') && !file_exists( AMP_LOCAL_PATH . $this->stylesheet )) {
            $this->removePlugin( 'Stylist');
        } else {
            $this->width += 200;
        }
        $plugins = count( $this->plugins )?"\n[\n". join(",\n", $this->plugins)."\n]":"null";

        $output = "";
        #$output  = "<script type=\"text/javascript\">\n";
        $output .= "xinha_editors = \n[\n". join(",\n", $this->editors)."\n];\n";
        $output .= "xinha_plugins = $plugins;\n";
        $output .= $this->_xinha_config();
        $this->_header->addJavascriptDynamicPrefix( $output, 'editor_config_dynamic');

        #$output .= "</script>\n";

        // this has to be included manually because AMP/Content/Header doesn't support
        // ordering of Dynamic and non-dynamic scripts -- this script has to run after the dynamic config above'
        #$legacy_output = "<script type=\"text/javascript\" src=\"".$this->config_location."\"></script>\n";
        $this->_header->addJavascript( $this->config_location, 'editor_config ');
        return false;
        
    }

    function _xinha_config() {
        $output = "function xinha_config(){ \n";
        $output .= "    cfg = new HTMLArea.Config();\n";
        $output .= "    cfg.width  = '".$this->width."px';\n";
        $output .= "    cfg.height = '".$this->height."px';\n";
        if ($this->isPlugin('Stylist') && file_exists( AMP_LOCAL_PATH . $this->stylesheet )) {
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
