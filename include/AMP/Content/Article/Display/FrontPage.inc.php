<?php

require_once ('AMP/Content/Article/Display.inc.php' );

class ArticleDisplay_FrontPage extends Article_Display {

    var $_more_link_text = "Read More&nbsp;&#187;";
    var $css_class = array(
        'title' => 'hometitle',
        'subtitle' => 'subtitle' );


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
        $image->setStyleAttrs( array( 'class' => 'image_frontpage' ));
        return PARENT::_HTML_imageBlock( $image );
    }
        

    function _HTML_title( $base_title ) {
        if ($href = $this->_article->getMoreLinkURL() ) {
            $title = $this->_HTML_link( $href, $base_title, array( "class"=>$this->css_class['title'] ) ); 
        } else {
            $title = $this->_HTML_inSpan( $base_title, $this->css_class['title'] );
        }
        return  $this->_HTML_in_P( $title, array("class" => "hometitle" ) );
    }

    function _HTML_subTitle( $subtitle ) {
        if (!$subtitle) return false;
        return $this->_HTML_inSpan( $subtitle , $this->css_class['subtitle'] ). $this->_HTML_newline();
    }


    function _HTML_bodyText ( $text ) {
        return $this->_HTML_inSpan( $this->_HTML_in_P( $text, array('class' => 'homebody') ), 'homebody' );
    }

    function _HTML_moreLink( $href ) {
        if (!$href) return false;
        return  $this->_HTML_inSpan( 
                    $this->_HTML_link(  $href, 
                                        $this->_getMoreLinkText(), 
                                        array( 'class' => 'morelink' )
                                      ), 
                    'morelink'  
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
