<?php

require_once( 'AMP/Content/Article/Display.inc.php' );

class ArticleDisplay_News extends Article_Display {

    function ArticleDisplay_News( &$article ) {
        $this->init( &$article );
    }

    function _HTML_Header() {
        $article = &$this->_article;
        
        return  $this->_HTML_start().
                $this->_HTML_title(     $article->getTitle() ).
                $this->_HTML_subTitle(  $article->getSubTitle() ).
                $this->_HTML_authorSource(  $article->getAuthor(), 
                                            $article->getSource(), 
                                            $article->getSourceURL() ).
                $this->_HTML_contact(   $article->getContact() ).
                $this->_HTML_date(      $article->getArticleDate() ) .
                $this->_HTML_endHeading();
    }

    function _HTML_title( $title ) {
        if (!$title) return false;
        return  $this->_HTML_inSpan( converttext( $title ), 'newstitle') . $this->_HTML_newline(2);
    }

    
    function _HTML_subTitle( $subtitle ) {
        if (!$subtitle) return false;
        return $this->_HTML_inSpan( converttext( $subtitle ) , 'newssubtitle' ). $this->_HTML_newline(2);
    } 
}
?>

