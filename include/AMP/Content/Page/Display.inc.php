<?php

define ('AMP_CONTENT_PAGE_DISPLAY_DEFAULT', 'standard' );
define ('AMP_CONTENT_PAGE_DISPLAY_PRINTERSAFE', 'printerSafe' );
define ('AMP_CONTENT_PAGE_DISPLAY_CONTENT', 'content' );

class AMPContent_PageDisplay {

    var $page;
    var $_requires_navigation;

    function AMPContent_PageDisplay( &$page ) {
        $this->init( $page );
    }

    function init( &$page ) {
        $this->page = &$page;
    }

    function execute($display_type = AMP_CONTENT_PAGE_DISPLAY_DEFAULT ) {
        $output_method = 'output_' . ucfirst( $display_type );
        if (!method_exists($this, $output_method)) $output_method = 'output_Standard';
        return $this->$output_method();
    }

    function output_Standard() {
        $this->page->template->placeNavigation();
        return  $this->page->header->output().
                $this->page->template->execute( $this->page->contentManager->output() );
    }

    function output_Content() {
        return $this->page->contentManager->output();
    }

    function output_PrinterSafe() {
        return  $this->page->header->output() .
                $this->_HTML_printSafeHead() . 
                $this->execute( AMP_CONTENT_PAGE_DISPLAY_CONTENT ) .
                $this->_HTML_endPage();
    }

    function _HTML_printSafeHead() {
        return "<div class=printer_safe_top></div>";
    }

    function _HTML_endPage() {
        return "</body></html>";
    }

}
?>
