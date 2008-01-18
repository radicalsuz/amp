<?php
require_once( 'AMP/Display/List.php' );
require_once( 'AMP/Content/RSS/Feed.inc.php' );

class RSS_Feed_Public_List extends AMP_Display_List {
    var $_source_criteria = array( 'displayable' => 1 );
    var $_source_object = "AMPContent_RSSFeed";
    var $_css_class_title = AMP_CONTENT_CSS_CLASS_LIST_ARTICLE_TITLE;
    var $_css_class_blurb = AMP_CONTENT_CSS_CLASS_LIST_BLURB;
    var $_css_class_container_list = "rss_feed_public_list list_block";
 
    function RSS_Feed_Public_List( $source = null, $criteria = array( ), $limit = null ) {
        $this->__construct( $source, $criteria, $limit );
    }

    function _renderItem( $source ) {
        return $this->render_name( $source )
                . $this->render_blurb( $source );
    }

    function execute( ) {
        $default_feed = new AMPContent_RSSFeed( AMP_Registry::getDbcon( ));
        $default_feed->setDefaults( );
        $default_feed->mergeData( array( "title" => "Latest Updates", "description" => "The most recently added pages on this website"));
        array_unshift( $this->_source, $default_feed );
        return parent::execute( );
    }

    function render_name( $source ) {
        return $this->_renderer->link( $source->getURL( ), $source->getName( ), array( 'class' => $this->_css_class_title ));
    }

    function render_blurb( $source ) {
        return $this->_renderer->div( $source->getBlurb( ), array( 'class' => $this->_css_class_blurb ));
    }

}

?>
