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
                . join( $this->_content_chunk_delimiter, $this->_content_chunks )
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
        return 'function ' . $this->_onload_script_name . '( ) {' . "\n";
    }

    function _jsEnd( ){
        return $this->_jsEndFunction( )
               $this->_jsFunctionCall( );
    }

    function _jsEndFunction( ){
        return "}\n";
    }

    function _jsFunctionCall( ){
        return 'window.onload  = '. $this->_onload_script_name . '( );' ."\n";
    }
}

?>
