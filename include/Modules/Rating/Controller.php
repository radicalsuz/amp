<?php
require_once( 'AMP/System/Component/Controller.php');
require_once( 'AMP/Content/Article.inc.php');

class Rating_Controller extends AMP_System_Component_Controller_Map {

    function Rating_Controller( ) {
        $this->__construct( );
    }

    function commit_list( ) {
        AMP_flush_common_cache( );
        #$ratings = AMP_lookup( 'article_ratings');
        #$top_20 = array_slice( array_keys( $ratings), 0, 20 );
        #$top_articles = array_map( array( $this, 'make_article' ), $top_20 );
        #$list = $this->_map->getComponent( 'list', $top_articles );
        $this->add_component_header( 'All Time - Top Rated' , '' );
        $list = $this->make_ratings_list( AMP_lookup( 'article_ratings'));
        $this->_display->add( $list, 'list_all' );

        $this->add_component_header( 'Last 7 days - Top Rated', '', 'banner');
        $list_weekly = $this->make_ratings_list( AMP_lookup( 'article_ratings_last_week'));
        $this->_display->add( $list_weekly, 'list_weekly' );
        $this->add_component_header( 'Last 30 days - Top Rated', '', 'banner');
        $list_monthly = $this->make_ratings_list( AMP_lookup( 'article_ratings_last_month'));
        $this->_display->add( $list_monthly, 'list_monthly' );

        return true;
    }

    function make_ratings_list( $ratings ) {
        $top_20 = array_slice( array_keys( $ratings), 0, 20 );
        $top_articles = array_map( array( $this, 'make_article' ), $top_20 );
        $list = $this->_map->getComponent( 'list', $top_articles );
        $list->ratings = $ratings;
        return $list;
    }

    function make_article( $article_id ) {
        return new Article( AMP_Registry::getDbcon( ), $article_id );
    }
}

?>
