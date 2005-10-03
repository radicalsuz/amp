<?php
require_once( 'AMP/Content/Page.inc.php');

class AMPContent_PageDisplay {

    var $_page;
    var $_template;

    /**
     * References the Page Header object 
     * 
     * @var AMPContent_Header       
     * @since 3.5.3
     * @access public
     */
    var $_header;

    function AMPContent_PageDisplay( &$page ) {
        $this->init( $page );
    }

    function init( &$page ) {
        $this->_page = &$page;
        $this->_header = &AMPContent_Header::instance( $page );
        $this->initTemplate( );
    }

    function &instance( &$page ) {
        static $page_display = false;
        if ( !$page_display ) $page_display = new AMPContent_PageDisplay( $page );
        return $page_display;
    }

    function execute($display_type = AMP_CONTENT_PAGE_DISPLAY_DEFAULT ) {
        $output_method = 'output_' . ucfirst( $display_type );
        if (!method_exists($this, $output_method)) $output_method = 'output_Standard';
        return $this->$output_method();
    }

    function output_Standard() {

        $this->_template->placeNavigation( $this->_page );
        $output =  
                $this->_header->output().
                $this->_template->execute( $this->_page->contentManager->output() );
        return $output;
    }

    function output_Content() {
        return $this->_page->contentManager->output();
    }

    function output_PrinterSafe() {
        return  $this->_header->output() .
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

    /**
     * initializes an AMPContent_Template  
     * 
     * @access public
     * @return void
     */
    function initTemplate() {
        $this->setTemplate( $this->_page->getTemplateId());
        $this->_initStyleSheets();
        $this->_addExtraHeader( );
    }

    /**
     * set the Template for use on the current page 
     * 
     * @param   integer     $template_id    The database id of the Template to use 
     * @access  public
     * @since   3.5.3
     * @return  void
     */
    function setTemplate( $template_id ) {
        require_once('AMP/Content/Template.inc.php' );
        $template = & new AMPContent_Template( $this->_page->dbcon, $template_id );
        if (!$template->hasData()) return false;
        $template->setPage( $this->_page );

        $this->_template = &$template;
        $template->globalizeNavLayout();
    }

    function _addExtraHeader( ) {
        if ( !( $extraHeader = $this->_template->getPageHeader( ))) return false;
        $this->_header->addExtraHtml( $extraHeader );
    }


    ##############################
    ###  StyleSheet accessors  ###
    ##############################


    /**
     * addStyleSheets 
     * 
     * @param mixed $css 
     * @access public
     * @return void
     */
    function addStyleSheets( $css ) {
        $stylesheet_array = array( $css );
        if (strpos($css, ",")!==FALSE) $stylesheet_array = preg_split( "/\s?,\s?/", $css );
        foreach ($stylesheet_array as $sheet_url ) {
            $this->_header->addStyleSheet( $sheet_url );
        }
    }

    /**
     * _initStyleSheets 
     * 
     * @access protected
     * @return void
     */
    function _initStyleSheets() {
        $map = &AMPContent_Map::instance( );
        if (!($css = $map->readAncestors( $this->_page->getSectionId(), 'css' ))) {
            $css = $this->_template->getCSS();
        }
        $this->addStyleSheets( $css );

        return true;
    }
}
?>
