<?php

class AMP_Form_Element_Wysiwyg {

    var $_renderer;
    var $_header;
    var $extras;
    var $extra_stylesheet=
             "cfg.stylistLoadStylesheet('%s');";
    var $stylesheet = '/custom/wysiwyg_styles.css';

    var $width = "90%";
    var $height = "420px";

    //var $_plugins = array( );

    var $_plugins = array(
            "SpellChecker",
            "FullScreen",
            "FindReplace",
            "Stylist",
			"TableOperations",
            "SuperClean",
            );

    var $id = false;

    function AMP_Form_Element_Wysiwyg( $id = null ) {
        $this->__construct( $id );
    }

    function __construct( $id = null ) {
        $this->_header = AMP_get_header( );
        $this->_renderer = AMP_get_renderer( );
        if ( !empty( $id )) {
            $this->identify( $id );
        }
    }

    function identify( $id ) {
        $this->id = $id;
    }

    function execute( ) {
        $this->_header->addJavascriptDynamicPrefix( $this->editor_location( ), 'xinha_config_location');
        $this->_header->addJavascript( 'scripts/xinha/XinhaCore.js', 'htmlarea' );
        //$this->_header->addJavascriptOnLoad( 'Xinha._addEvent( $( "'.$this->id.'"), "click", xinha_init_'.str_replace( " ", '_', $this->id ).' );');
        $this->_header->addJavascriptOnLoad( 'Xinha._addEvent( window, "load", xinha_init_'.str_replace( " ", '_', $this->id ).' );');
        $this->_header->addJavascriptDynamic( $this->init_script( ), 'xinha_init_'. $this->id );
        
    }

    function editor_location( ) {
        return 
<<<JAVASCRIPT
_editor_url="/scripts/xinha";
_editor_lang="en";
JAVASCRIPT;
    }

    function init_script( ) {

        $new_script = 
<<<JAVASCRIPT
xinha_init_%6\$s=function( )
{
if ( !Xinha.loadPlugins( [%2\$s], xinha_init_%6\$s )) return; 

cfg = new Xinha.Config( );
cfg.width = '%3\$s';
cfg.height = '%4\$s';
%5\$s

xinha_editors   = Xinha.makeEditors([%1\$s], cfg, [%2\$s]);
Xinha.startEditors( xinha_editors );

}

JAVASCRIPT;
        return sprintf( $new_script, $this->list_editors( ), $this->list_plugins( ), $this->width, $this->height, $this->render_extras( ), str_replace( ' ', '_', $this->id ));
    }

    function list_editors( ) {
        return $this->delimit( $this->id );
    }

    function list_plugins( ) {
        return $this->delimit( $this->_plugins );
    }

    function render_extras( ) {
        $this->extras = "";
        //wysiwyg styles plugin
        if ( file_exists( AMP_LOCAL_PATH . $this->stylesheet )) {
            $this->extras = sprintf( $this->extra_stylesheet, $this->stylesheet );
        }
        //allow javascripts
        if ( defined( 'AMP_USERMODE_ADMIN') && AMP_USERMODE_ADMIN ) {
            $this->extras .= "\ncfg.stripScripts = false;" ;
        }
        return $this->extras;
    }

    function delimit( $value ) {
        if ( empty( $value )) return false;
        if ( !is_array( $value )) {
            return "'".$value."'";
        }

        $new_values = array( );
        foreach( $value as $value_segment ) {
            $new_values[] = "'".$value_segment."'";
        }
        return join( ",", $new_values );
    }
}

?>
