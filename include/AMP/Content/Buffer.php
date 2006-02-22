<?php

class AMP_Content_Buffer {
    var $_content_chunks = array( );
    var $_content_chunk_delimiter = '';

    var $_header;
    var $_footer;

    function add( $content, $key = null ){
        if ( !isset( $key )) return $this->_content_chunks[] = $content;
        return $this->_content_chunks[ $key ] = $content;
    }

    function execute( ){
        if ( empty( $this->_content_chunks )) return false;
        return    $this->_header
                . join( $this->_content_chunk_delimiter, $this->_content_chunks )
                . $this->_footer;
    }

    function output( ){
        return $this->execute( );
    }

    function clear( ){
        $this->_content_chunks = array( );
    }

    function set_header( $header ){
        $this->_header = $header;
    }

    function set_footer( $footer ){
        $this->_footer = $footer;    
    }

    function set_delimiter( $delimiter ){
        $this->_content_chunk_delimiter = $delimiter;
    }

}

?>
