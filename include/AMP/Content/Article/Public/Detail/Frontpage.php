<?php

require_once( 'AMP/Content/Article/Public/Detail.php');

class Article_Public_Detail_Frontpage extends Article_Public_Detail {

    var $_more_link_text = AMP_TEXT_CONTENT_FRONTPAGE_MORELINK;

    var $_css_class_title = "hometitle";
    var $_css_class_text = "homebody";
    var $_css_class_morelink = "morelink";
    var $_css_class_image = "image_frontpage";
    var $_css_class_subtitle = AMP_CONTENT_CSS_CLASS_ARTICLE_FRONTPAGE_SUBTITLE;

    function Article_Public_Detail_Frontpage( $source ) {
        $this->__construct( $source );
    }

    function renderItem( $source ) {
        
        return 
                $this->render_image( $source )
                . $this->render_title( $source )
                . $this->render_subtitle( $source )
                . $this->render_body( $source )
                . $this->render_morelink( $source )
                ;
    }

    function render_title( $source ) {
        $morelink_url = $source->getMoreLinkURL( );
        $title = $source->getTitle( );
        if( !$title ) return false;
        return $this->_renderer->div( $this->_renderer->link( $morelink_url, $title, array( 'class' => $this->_css_class_title )), array( 'class' => $this->_css_class_title ));
    }

    function render_morelink( $source ) {
        $morelink_url = $source->getMoreLinkURL( );
        if( !$morelink_url ) return false;
        return $this->_renderer->link( $morelink_url, $this->_more_link_text, array( 'class' => $this->_css_class_morelink ));
    }
}


?>
