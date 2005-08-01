<?php

class AMPContent_Manager {

    var $_html_body;
    var $_html_footer;

    function AMPContent_Manager() {
        $this->init();
    }

    function init() {
    }

    function setBody( $html ) {
        $this->_html_body = $html;
    }

    function setFooter( $html ) {
        $this->_html_footer = $html;
    }

    function appendBody( $html ) {
        if (!isset($this->_html_body)) return $this->setBody( $html );
        $this->_html_body .= $html;
    }

    function appendFooter( $html ) {
        if (!isset($this->_html_footer)) return $this->setFooter( $html );
        $this->_html_footer .= $html;
    }

    function output() {
        return $this->_html_body .
               $this->_html_footer ;
    }

}
?>
