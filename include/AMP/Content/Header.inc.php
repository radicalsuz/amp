<?php
define( 'AMP_HTML_JAVASCRIPT_START', "\n<script language=\"Javascript\"  type=\"text/javascript\">\n//<!--\n\n");
define( 'AMP_HTML_JAVASCRIPT_END', "\n//-->\n</script>\n");
define( 'AMP_HTML_STYLESHEET_START', "<style type='text/css'>\n");
define( 'AMP_HTML_STYLESHEET_END', "</style>\n");

define( 'AMP_HTML_JAVASCRIPTPREFIX_START', "<script language=\"Javascript\"  type=\"text/javascript\">\n");
define( 'AMP_HTML_JAVASCRIPTPREFIX_END', "</script>\n");

class AMPContent_Header {

    var $_title_separator =  "&nbsp;:&nbsp;";
    var $encoding = AMP_SITE_CONTENT_ENCODING ;
    var $_page;

    var $link_rel = array( 'Search'=> '/search.php' );
    var $javaScripts = array( 'functions' => '/scripts/functions.js' );
    var $_javaScript_buffer;
    var $_javaScriptPrefix_buffer;

    var $styleSheets = array( 'default'		=> '/styles_default.css');
    var $_styleSheet_buffer;

    var $_path_favicon = '/img/favicon.ico';
    var $_extraHtml;

    function AMPContent_Header( &$page ) {
        $this->init( $page );
    }

    function init( &$page ) {
        $this->setPage( $page );
    }


    function &instance( &$page ) {
        static $request_header = false;
        if ( !$request_header ) $request_header = new AMPContent_Header( $page );
        return $request_header;
    }

    function setPage( &$page ) {
        $this->_page = &$page;
    }

    function getPageTitle() {
        $pageTitle = array( AMP_SITE_NAME );
        $article = &$this->_page->getArticle( );
        $section = &$this->_page->getSection( );
        $introtext = &$this->_page->getIntroText( );

        if ($introtext && ( $title = $introtext->getTitle( ))) {
            $pageTitle[] = strip_tags($title);
        } elseif ($article && ( $title = $article->getTitle( ))) {
            $pageTitle[] = strip_tags($title);
        } elseif ($section && ( $title = $section->getName( ))) {
            $pageTitle[] = strip_tags($title);
        }
        
        $this->_pageTitle = join( $this->_title_separator, $pageTitle ); 
        return $this->_pageTitle;
    }

    function output () {
        return      $this->_HTML_startHeader() . 
                    $this->_HTML_header() .
                    $this->_HTML_endHeader();
    }

    function getMetaDesc() {
        $metadesc = AMP_SITE_META_DESCRIPTION ;

        if ( $section = &$this->_page->getSection() && ($section->id != AMP_CONTENT_MAP_ROOT_SECTION) ) {
            $metadesc = $section->getBlurb();
        }
        if ( $article = &$this->_page->getArticle() ) {
            $metadesc = $article->getBlurb();
        }

        return $this->stripQuotes($metadesc);

    }

    function stripQuotes( $text ) {
        return str_replace( '"', '', $text );
    }

    function getMetaKeywords() {
        return $this->stripQuotes( AMP_SITE_META_KEYWORDS );
    }

    function addJavaScript( $script_url, $id = null) {
        if (!$script_url) return false;
        if (isset($id)) {
            $this->javaScripts[ $id ] = $script_url ;
            return true;

        }
        return ($this->javaScripts[] = $script_url);
    }

    function addJavascriptDynamic( $content, $key = null ){
        $buffer = &$this->_getBuffer( 'javaScript' );
        return $buffer->add( $content, $key );
    }

    function addJavascriptDynamicPrefix( $content, $key = null ){
        $buffer = &$this->_getBuffer( 'javaScriptPrefix' );
        return $buffer->add( $content, $key );
    }

    function addJavascriptOnLoad( $content, $key = null ){
        require_once( 'AMP/System/Page/OnLoad.php');
        $onload_script = & AMP_System_Page_OnLoad::instance( );
        $onload_script->add( $content, $key );
    }

    function &_getBuffer( $buffer_type = 'javaScript' ){
        $buffer_Ref = '_' . $buffer_type . '_buffer';
        if ( isset( $this->$buffer_Ref )) return $this->$buffer_Ref;

        $buffer_header_constant = 'AMP_HTML_'.strtoupper( $buffer_type ).'_START'; 
        $buffer_header = defined( $buffer_header_constant ) ?
                         constant( $buffer_header_constant ) : false;

        $buffer_footer_constant = 'AMP_HTML_'.strtoupper( $buffer_type ).'_END'; 
        $buffer_footer = defined( $buffer_footer_constant ) ?
                         constant( $buffer_footer_constant ) : false;

        $this->$buffer_Ref = &AMP_initBuffer( $buffer_header, $buffer_footer );
        return $this->$buffer_Ref;
    }


    function addStylesheet( $link, $id = null) {
        if (!$link) return false;
        if (isset($id)) return ($this->styleSheets[ $id ] = $link);
        return ($this->styleSheets[] = $link );
    }

    function addStylesheetDynamic( $content, $key = null ){
        $buffer = &$this->_getBuffer( 'styleSheet' );
        return $buffer->add( $content, $key );
    }

    function addExtraHtml( $html ) {
        $this->_extraHtml .= $html;
    }


    function _HTML_startHeader() {
        return "<html>\n<head>\n";
    }

    function _HTML_linkRels() {
        $output = "";
        foreach ( $this->link_rel as $relationship => $url ) {
            $output .= "<link rel=\"" . $relationship . "\" href=\"" . $url . "\">\n";
        }
        return $output . $this->_HTML_FavIcon() . $this->_HTML_styleSheets() . $this->_HTML_feed();
    }

    function _HTML_FavIcon() {
        $favicon_path = $this->_getFaviconPath( );
        return '<link rel="icon" href="'. $favicon_path .'" type="image/x-icon" />' . "\n";
        
    }

    function _getFaviconPath( ){
        if ( file_exists( AMP_LOCAL_PATH . $this->_path_favicon ) ) return $this->_path_favicon; 
        return false;
    }

    function _HTML_pageTitle() {
        return "<title>". $this->getPageTitle( )."</title>\n";
    }

    function _HTML_feed() {
        $rss_vars = array( );
        if ( $this->_page->isList( AMP_CONTENT_LISTTYPE_SECTION )) {
            $section = &$this->_page->getSection( );
            $rss_vars['section'] = 'section=' . $section->id;
        }
        if ( $this->_page->isList( AMP_CONTENT_LISTTYPE_CLASS )) {
            $class= &$this->_page->getClass( );
            $rss_vars['class'] = 'class=' . $class->id;
        }
        $final_rssfeed_url = empty( $rss_vars ) ? 
                            AMP_SITE_URL . AMP_CONTENT_URL_RSSFEED : 
                            AMP_Url_AddVars( AMP_SITE_URL . AMP_CONTENT_URL_RSSFEED, $rss_vars );
        return "<link rel=\"alternate\" type=\"application/rss+xml\" title=\"RSS\" href=\"".$final_rssfeed_url."\">";
    }


    function _HTML_styleSheets() {
        $output = "";
        foreach ( $this->styleSheets as $css_id => $url ) {
            $url = trim( $url );
            if (!(substr($url, 0, 4)=="http") && !(substr($url, 0, 1)=="/")) $url = "/" . $url;
            $output .= "<link rel=\"stylesheet\" type=\"text/css\" href=\"" . $url . "\">\n";
        }
        if ( isset( $this->_styleSheet_buffer )) {
            $output .= $this->_styleSheet_buffer->execute( );
        }
        return $output ;
    }

    function _HTML_javaScripts() {
        require_once( 'AMP/System/Page/OnLoad.php');
        $onload_script = & AMP_System_Page_OnLoad::instance( );
        $onload_script->execute( );

        $output = "";
        if ( isset( $this->_javaScriptPrefix_buffer)) {
            $output .= $this->_javaScriptPrefix_buffer->execute( );
        }
        foreach ( $this->javaScripts as $script_id => $url ) {
            if (!((substr($url, 0, 1)=="/") || (substr($url, 0, 7)=="http://"))) $url = "/" . $url;
            $output .= "<script language=\"Javascript\"  type=\"text/javascript\" src=\"" . $url . "\"></script>\n";
        }
        if ( isset( $this->_javaScript_buffer)) {
            $output .= $this->_javaScript_buffer->execute( );
        }
        return $output;
    }

    function _HTML_metaTags() {
        $tags = "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=".$this->encoding."\">\n";
        if ( $metadesc = $this->getMetaDesc() ) {
            $tags .= "<meta http-equiv=\"Description\" content=\"". $metadesc. "\">\n" ;
        }
        if ( $metacontent = $this->getMetaKeywords() ) {
            $tags .= "<meta name=\"Keywords\" content=\"" . $metacontent . "\">\n" ;
        }
        return $tags;
    }

    function _HTML_extra() {
        return $this->_extraHtml;
    }


    function _HTML_header () {
        return  $this->_HTML_metaTags() . 
                $this->_HTML_linkRels() . 
                $this->_HTML_pageTitle() . 
                $this->_HTML_javaScripts() .
                $this->_HTML_extra();
    }

    function _HTML_endHeader() {
        return "</head>\n";
    }

        

}


?>
