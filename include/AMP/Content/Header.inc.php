<?php

class AMPContent_Header {

    var $title_separator =  "&nbsp;:&nbsp;";
    var $encoding = 'iso-8859-1';
    var $registry;
    var $page;

    var $link_rel = array( 'Search'=> '/search.php' );
    var $javaScripts = array( 'functions' => '/scripts/functions.js' );
    var $styleSheets = array( 'default'		=> '/styles_default.css');

    function AMPContent_Header( &$page ) {
        $this->init( $page );
    }

    function init( &$page ) {
        $this->registry = &AMP_Registry::instance();
        $this->page = &$page;
        $this->verifyEncoding();
    }

    function verifyEncoding() {
        if (!($encoding = $this->registry->getEntry( AMP_REGISTRY_SETTING_ENCODING ))) return false;
        $this->encoding = $encoding;
    }

    function setPageTitle() {
        $this->_pageTitle = $this->registry->getEntry( AMP_REGISTRY_SETTING_SITENAME );
        if (!($title = $this->registry->getEntry( AMP_REGISTRY_CONTENT_PAGE_TITLE ))) return false;
        $this->_pageTitle = join( $this->title_separator, array( $this->_pageTitle, strip_tags($title) ) );
    }

    function output () {
        $this->setPageTitle();
        return      $this->_HTML_startHeader() . 
                    $this->_HTML_header() .
                    $this->_HTML_endHeader();
    }

    function getMetaDesc() {
        if ( $article = &$this->registry->getArticle() ) {
            return $this->stripQuotes( $article->getBlurb());
        }
        if ( $section = &$this->registry->getSection() ) {
            return $this->stripQuotes( $section->getBlurb());
        }

        return $this->stripQuotes( $this->registry->getEntry( AMP_REGISTRY_SETTING_METADESCRIPTION ));
    }

    function stripQuotes( $text ) {
        return str_replace( '"', '', $text );
    }

    function getMetaContent() {
        return $this->stripQuotes( $this->registry->getEntry( AMP_REGISTRY_SETTING_METACONTENT ));
    }

    function addJavaScript( $script_url, $id = null) {
        if (!$script_url) return false;
        if (isset($id)) return ($this->styleSheets[ $id ] = $script_url );
        return ($this->styleSheets[] = $script_url);
    }

    function addStylesheet( $link, $id = null) {
        if (!$link) return false;
        if (isset($id)) return ($this->styleSheets[ $id ] = $link);
        return ($this->styleSheets[] = $link );
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
        if ( file_exists( AMP_LOCAL_PATH . "/img/favicon.ico" ) ) {
            return '<link rel="icon" href="/img/favicon.ico" type="image/x-icon" />' . "\n";
        }
    }

    function _HTML_pageTitle() {
        return "<title>". $this->_pageTitle ."</title>\n";
    }

    function _HTML_feed() {
        return "<link rel=\"alternate\" type=\"application/rss+xml\" title=\"RSS\" href=\"".AMP_SITE_URL.AMP_CONTENT_URL_RSSFEED."\">";
    }


    function _HTML_styleSheets() {
        $output = "";
        if (empty($this->styleSheets)) return false;
        foreach ( $this->styleSheets as $css_id => $url ) {
            if (!(substr($url, 0, 1)=="/")) $url = "/" . $url;
            $output .= "<link rel=\"stylesheet\" type=\"text/css\" href=\"" . $url . "\">\n";
        }
        return $output;
    }



    function _HTML_javaScripts() {
        $output = "";
        if (empty($this->javaScripts)) return false;
        foreach ( $this->javaScripts as $script_id => $url ) {
            if (!((substr($url, 0, 1)=="/") || (substr($url, 0, 7)=="http://"))) $url = "/" . $url;
            $output .= "<script language=\"Javascript\"  type=\"text/javascript\" src=\"" . $url . "\"></script>\n";
        }
        return $output;
    }

    function _HTML_metaTags() {
        $tags = "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=".$this->encoding."\">\n";
        if ( $metadesc = $this->getMetaDesc() ) {
            $tags .= "<meta http-equiv=\"Description\" content=\"". $metadesc. "\">\n" ;
        }
        if ( $metacontent = $this->getMetaContent() ) {
            $tags .= "<meta name=\"Keywords\" content=\"" . $metacontent . "\">\n" ;
        }
        return $tags;
    }

    function _HTML_templateHead() {
        $page = &AMPContent_Page::instance();
        if (!($html = $page->template->getPageHeader() )) return false;
        return $html;
    }


    function _HTML_header () {
        return  $this->_HTML_metaTags() . 
                $this->_HTML_linkRels() . 
                $this->_HTML_pageTitle() . 
                $this->_HTML_javaScripts() .
                $this->_HTML_templateHead();
    }

    function _HTML_endHeader() {
        return "</head>\n";
    }

        

}


?>
