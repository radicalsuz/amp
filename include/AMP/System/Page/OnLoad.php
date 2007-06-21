<?php
require_once( 'AMP/Content/Buffer.php');

class AMP_System_Page_OnLoad extends AMP_Content_Buffer {

    var $_onload_script_name = 'AMP_System_Page_Onload';
    var $_content_chunks_delimiter = "\n";

    function &instance( ){
        static $onload_script_factory = false;
        if ( !$onload_script_factory ) $onload_script_factory = new AMP_System_Page_OnLoad;
        return $onload_script_factory;
    }

    function execute( ){
        if ( empty( $this->_content_chunks )) return false;
        $output = $this->render( );
        $this->commit( $output );
        return $output; 
    }

    function render( ){
        return    $this->_header
                . $this->_jsStart( )
                . join( $this->_content_chunks_delimiter, $this->_content_chunks )
                . $this->_jsEnd( )
                . $this->_footer;

    }

    function commit( $script_source ){
        $page_header = & AMP_getHeader( );
        return $page_header->addJavascriptDynamic( $script_source, 'onload');
    }

    function _jsStart( ){
        return $this->_jsFunctionDeclare( );
    }

    function _jsFunctionDeclare( ){
        return "\nfunction " . $this->_onload_script_name . '( ) {' . "\n";
    }

    function _jsEnd( ){
        return $this->_jsEndFunction( ).
               $this->_jsFunctionCall( );
    }

    function _jsEndFunction( ){
        return "\n}\n";
    }

    function _jsFunctionCall( ){
        $attach_script = 
<<<JAVASCRIPT
if (window.addEventListener) //DOM method for binding an event
    window.addEventListener("load", %1\$s, false)
else if (window.attachEvent) //IE exclusive method for binding an event
    window.attachEvent("onload", %1\$s )
else if (document.getElementById) //support older modern browsers
    window.onload=%1\$s;
JAVASCRIPT;
        return sprintf( $attach_script, $this->_onload_script_name );
        //return 'window.onload  = '. $this->_onload_script_name . ';' ."\n";
    }
}

?>
