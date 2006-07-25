<?php

require_once ('AMP/Content/Article/Display.inc.php' );
if (!defined( 'AMP_TEXT_CONTENT_FRONTPAGE_MORELINK' )) define( 'AMP_TEXT_CONTENT_FRONTPAGE_MORELINK', 'Read More&nbsp;&raquo;' );
if (!defined( 'AMP_ARTICLE_FRONTPAGE_DISPLAY_CSS_SUBTITLE')) define( 'AMP_ARTICLE_FRONTPAGE_DISPLAY_CSS_SUBTITLE', 'subtitle' );

class ArticleDisplay_FrontPage extends Article_Display {

    var $_more_link_text = AMP_TEXT_CONTENT_FRONTPAGE_MORELINK;

    var $_css_class_title = "hometitle";
    var $_css_class_text = "homebody";
    var $_css_class_morelink = "morelink";
    var $_css_class_image = "image_frontpage";
    var $_css_class_subtitle = AMP_ARTICLE_FRONTPAGE_DISPLAY_CSS_SUBTITLE;


    function ArticleDisplay_Frontpage ( &$article ) {
        $this->init( $article );
    }

    function _HTML_Header( ) {
        $article = &$this->_article;
        $header =  
            $this->_HTML_title( $article->getTitle() ) . 
            $this->_HTML_subTitle( $article->getSubTitle() ) ; 
        return $this->_addImage( $header );
    }

    function _HTML_Content() {
        if (!( $body = $this->_article->getBody() )) return false;
        return $this->_processBody( $body );
    }
        
    function _HTML_Footer() {
        return  $this->_HTML_moreLink( $this->_article->getMoreLinkURL() ).
                $this->_HTML_end();
    }

    function _HTML_imageBlock( &$image ) {
        if (!$image) return false;
        #$image->setStyleAttrs( array( 'border' => '1', 'style' => 'border-color:#000000' ));
        $image->setStyleAttrs( array( 'class' => $this->_css_class_image ));
        return PARENT::_HTML_imageBlock( $image );
    }
        

    function _HTML_title( $base_title ) {
        if ($href = $this->_article->getMoreLinkURL() ) {
            $title = $this->_HTML_link( $href, $base_title, array( "class"=>$this->_css_class_title ) ); 
        } else {
            $title = $this->_HTML_inSpan( $base_title, $this->_css_class_title );
        }
        return  $this->_HTML_in_P( $title, array("class" => $this->_css_class_title ) );
    }

    function _HTML_subTitle( $subtitle ) {
        if (!$subtitle) return false;
        return $this->_HTML_inSpan( $subtitle , $this->_css_class_subtitle ). $this->_HTML_newline();
    }


    function _HTML_bodyText ( $text ) {
        return $this->_HTML_inSpan( $this->_HTML_in_P( $text, array('class' => $this->_css_class_text ) ), $this->_css_class_text );
    }

    function _HTML_moreLink( $href ) {
        trigger_error( 'trying morelink ' . $href );
        if (!$href) return false;
        return  $this->_HTML_inSpan( 
                    $this->_HTML_link(  $href, 
                                        $this->_getMoreLinkText(), 
                                        array( 'class' => $this->_css_class_morelink )
                                      ), 
                    $this->_css_class_morelink  
                ).
                $this->_HTML_newline();
    }

    function _getMoreLinkText() {
        return $this->_more_link_text;
    }

    function _HTML_end () {
        return $this->_HTML_newline();
    }
}
?>
