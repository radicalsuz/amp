<?php

require_once( 'AMP/Content/Article/Display.inc.php' );

class ArticleDisplay_Introtext extends Article_Display {

    function ArticleDisplay_Introtext ( &$introtext ) {
        $this->init( $introtext );
    }

    function _HTML_Header() {
        $article = &$this->_article;

        return  $this->_HTML_start() .
                $this->_HTML_title( $article->getTitle() ) .
                $this->_HTML_subTitle( $article->getSubTitle() ) ;
    }

    function _HTML_Content() {
        $text = parent::_HTML_Content();
        return $this->_HTML_in_P( $this->_activateIncludes( $text ), array( 'class' => $this->_css_class_text ) );
    }

    function _HTML_Footer() {
        return  $this->_HTML_end() . $this->_HTML_newline();
    }
}
?>
