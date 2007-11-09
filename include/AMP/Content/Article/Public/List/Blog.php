<?php
require_once( 'AMP/Content/Article/Public/Detail/Blog.php');
require_once( 'AMP/Content/Article/Public/List.php');

class Article_Public_List_Blog extends Article_Public_List {
    var $_detail;
    var $_css_class_container_list_item = 'list_item list_item_blog';
    var $_css_class_title = 'title';

    function Article_Public_List_Blog( $source = false, $criteria = array( ), $limit = null ) {
        $this->__construct( $source, $criteria, $limit );
    }

    function _after_init( ) {
        $sample = $this->_source_sample;
        $detail_class = AMP_ARTICLE_DISPLAY_BLOG;
        $this->_detail = new $detail_class( $this->_source_sample );
    }

    function _renderItem( &$source ) {
        return  $this->render_title( $source )
                . $this->render_byline( $source )
                . $this->render_image( $source )
                . $this->render_body( $source )
                . $this->render_item_footer( $source );
    }

    function render_title( $source ) {
        if ( !( $title = $source->getName( ))) return false;
        return $this->_renderer->p( $this->_renderer->link( $source->getURL( ), converttext( $title )), array( 'class' => $this->_css_class_title ));
    }

    function render_image( $source ) {
        return $this->_detail->render_image( $source );
    }

    function render_body( $source ) {
        return $this->_detail->render_body( $source );
    }

    function render_byline( $source ) {
        return $this->_detail->render_byline( $source );
    }

    function render_item_footer( $source ) {
        $comments = $this->render_comments( $source );
        $sections = $this->render_sections( $source );
        $output = $sections . ( ( $comments && $sections ) ? $this->_renderer->separator( ) : '') . $comments;
        return $this->_renderer->div( $output, array( 'class' => 'blog_list_footer'));
    }

    function render_comments( $source ) {
        $comments = AMP_lookup( 'comments_live_by_article', $source->id );
        $text= ( empty( $comments ) ? AMP_TEXT_NO_COMMENTS : count( $comments ) . ' ' . AMP_pluralize( AMP_TEXT_COMMENT ));
        return $this->_renderer->link(AMP_Url_AddAnchor($source->getURL(), 'comments'), $text);
    }

    function render_sections( $source ) {
        return $this->_detail->render_sections( $source );
    }
}

?>
