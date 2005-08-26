<?php

define ( 'MEMCACHE_KEY_CONTENT' , 'PageContent' );
class AMPContent_Manager {

    var $_html_body;
    var $_html_footer;

    var $_show_list_intro = true;
    var $_displays = array();
    var $_intro_display;

    function AMPContent_Manager() {
        $this->init();
    }

    function init() {
    }

    function &instance() {
        static $manager = false;
        if (!$manager) $manager = new AMPContent_Manager;
        return $manager;
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
        $output=$this->doIntroDisplay().
                $this->_html_body .
                $this->doDisplays().
                $this->_html_footer ;
        return $output;
    }

    ######################################
    ### public display control methods ###
    ######################################

    function addDisplay( &$display, $name = null ) {
        if (isset($name)) {
            $this->_displays[ $name ] = &$display;
            return true;
        }
        $this->_displays[] = &$display;
    }

    function setIntroDisplay( &$display ) {
        $this->_intro_display = &$display;
    }

    function doIntroDisplay() {
        if (!isset($this->_intro_display)) return false;
        return $this->_intro_display->execute();
    }

    function doDisplays() {
        if (empty($this->_displays)) return false;
        $output = "";
        foreach ($this->_displays as $display ) {
            $output .= $display->execute();
        }

        return $output;
    }

    function setListIntro( $show_list = true ) {
        $this->_show_list_intro = $show_list;
    }

    function showListIntro() {
        return $this->_show_list_intro;
    }

}
?>
