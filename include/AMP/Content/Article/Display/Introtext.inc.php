<?php

require_once( 'AMP/Content/Article/Display.inc.php' );
define( 'AMP_INCLUDE_START_TAG', '{{' );
define( 'AMP_INCLUDE_END_TAG', '}}' );

class ArticleDisplay_Introtext extends Article_Display {

    function ArticleDisplay_Introtext ( &$introtext ) {
        $this->init( $introtext );
    }

    function _HTML_Header() {
        $article = &$this->_article;

        return $this->_HTML_start() .
                $this->_HTML_title( $article->getTitle() ) .
                $this->_HTML_subTitle( $article->getSubTitle() ) ;
    }

    function _HTML_Content() {
        $text = PARENT::_HTML_Content();
        return $this->_HTML_in_P( $this->_activateIncludes( $text ), array( 'class' => 'text' ) );
    }

    function _HTML_Footer() {
        return  $this->_HTML_end() . $this->_HTML_newline();
    }


    function _activateIncludes( $html ) {
        $start = $this->_findIncludeStartTag( $html );
        if ($start === FALSE) return $html;
        $start = $start + strlen( AMP_INCLUDE_START_TAG );

        $end = $this->_findIncludeEndTag( $html, $start );
        if ($end === FALSE) return $html;

        $result = $this->_processInclude( substr( $html, $start, $end-$start) );

        $block_end = $end + strlen( AMP_INCLUDE_END_TAG );
        $block_start = $start - strlen( AMP_INCLUDE_START_TAG );
        $current_html = $this->_replaceInclude( $html, $result, $block_start, $block_end );

        return $this->_activateIncludes( $current_html );
    }
    
    #########################################
    ###  Private include parsing methods  ###
    #########################################

    function _processInclude( $code ) {
        if (!($filename = $this->_getIncludeFilename( $code ))) return false;

        ob_start();
        include( $filename );
        $include_value = ob_get_contents();
        ob_end_clean();

        return $include_value;
    }

    function _getIncludeFilename( $code ) {
        $filename = trim ( $code );
        if (file_exists_incpath( $filename )) return $filename;
        
        return false;
    }

    function _findIncludeStartTag( $html, $offset = 0 ) {
        return strpos( $html, AMP_INCLUDE_START_TAG );
    }

    function _findIncludeEndTag( $html, $offset = 0 ) {
        return strpos( $html, AMP_INCLUDE_END_TAG );
    }

    function _replaceInclude( $original, $insert, $start, $end ) {
        return substr( $original, 0, $start) . $insert . substr($original, $end);
    }

}
?>
