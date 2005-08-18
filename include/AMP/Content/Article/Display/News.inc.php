<?php

define ('AMP_TEXT_CONTENT_FAIR_USE_HEADING', 'Fair Use Notice' );
if (!defined( 'AMP_CONTENT_FAIR_USE_NOTICE' )) define ('AMP_CONTENT_FAIR_USE_NOTICE', false );

require_once( 'AMP/Content/Article/Display.inc.php' );

class ArticleDisplay_News extends Article_Display {

    function ArticleDisplay_News( &$article ) {
        $this->init( $article );
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
                $this->_HTML_date(      $article->getItemDate() ) .
                $this->_HTML_endHeading();
    }

    function _HTML_end() {
        if (!AMP_CONTENT_FAIR_USE_NOTICE) return PARENT::_HTML_end();
        return $this->_HTML_fairUseNotice() . PARENT::_HTML_end();
    }

    function _HTML_title( $title ) {
        if (!$title) return false;
        return  $this->_HTML_inSpan( converttext( $title ), 'newstitle') . $this->_HTML_newline(2);
    }

    
    function _HTML_subTitle( $subtitle ) {
        if (!$subtitle) return false;
        return $this->_HTML_inSpan( converttext( $subtitle ) , 'newssubtitle' ). $this->_HTML_newline(2);
    } 

    function fairUseURL() {
        return 'http://www.law.cornell.edu/uscode/17/107.shtml'; 
    }

    function _HTML_fairUseLink() {
        
        return $this->_HTML_link( $this->fairUseURL(), $this->fairUseURL() );
    }

    function _HTML_fairUseNotice() {
        $notice =   $this->_HTML_bold( AMP_TEXT_CONTENT_FAIR_USE_HEADING ) . 
                    $this->_HTML_newline() . 
                    $this->getUseNotice() ;
        return $this->_HTML_newline(3) . $this->_HTML_inDiv( $notice, array( 'class' => 'fairUse' ) ) ; 
    }

    function getUseNotice() {
        return 
        "This site contains copyrighted material the use of which has not always been ".
        "specifically authorized by the copyright owner. We are making such material ".
        "available in our efforts to advance understanding of environmental, political, ".
        "human rights, economic, democracy, scientific, and social justice issues, etc. ".
        "We believe this constitutes a 'fair use' of any such copyrighted material as ".
        "provided for in section 107 of the US Copyright Law. In accordance with ".
        "Title 17 U.S.C. Section 107, the material on this site is distributed without ".
        "profit to those who have expressed a prior interest in receiving the included ".
        "information for research and educational purposes. For more information go to: ".
        $this->_HTML_fairUseLink() .
        ". If you wish to use copyrighted material from this site for purposes of your own that go beyond 'fair use', ".
        "you must obtain permission from the copyright owner. ";
    }
}
?>
