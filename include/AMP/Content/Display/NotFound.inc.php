<?php

require_once( 'AMP/Content/Display/HTML.inc.php');

class Display_NotFound extends AMPDisplay_HTML {

    var $_message = 'The item you requested was not found';
    var $_css_class_message = 'text';

    function Display_NotFound( $message = null ) {
        if ( isset( $message )) $this->_message = $message;
    }

    function execute( ) {
        return $this->_HTML_inDiv( $this->_message, array( 'class' => 'page_result'));
    }

}
?>
