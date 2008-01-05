<?php

require_once( 'AMP/Display/System/List.php' );
require_once( 'Modules/Rating/Rating.php' );
require_once( 'AMP/Content/Article.inc.php');

class Rating_List extends AMP_Display_System_List {

    var $columns = array( 'controls', 'name', 'rating', 'comments', 'emails' );
    var $column_headers = array( 'name' => 'Article', 'rating' => 'Avg Rating', 'comments' => 'Comments', 'emails' => 'Emailed' );
    var $_source_object = 'Article';

    var $_actions = array( );
    var $_suppress_toolbar = true;
    var $_suppress_create = true;

    var $ratings;

    function Rating_List( $source = false, $criteria = array( ), $limit = null ) {
        $this->__construct( $source, $criteria, $limit );
    }

    function _after_init( ) {
        $this->ratings = AMP_lookup( 'article_ratings');
        $this->comments = AMP_lookup( 'most_commented_articles');
        $this->shares = AMP_lookup( 'most_emailed_articles');
    }

    function prioritize( $column_name ) {
        $start = array( 'controls', 'name', $column_name );
        $others = array_diff( $this->columns, $start );
        $this->columns = array_merge( $start, $others );
    }

    function render_rating( $source ) {
        if( !isset( $this->ratings[ $source->id ])) return false;
        return sprintf( '%01.2f', $this->ratings[ $source->id ]);
    }

    function render_comments( $source ) {
        if( !isset( $this->comments[ $source->id ])) return false;
        return $this->comments[ $source->id ];
    }

    function render_emails( $source ) {
        if( !isset( $this->shares[ $source->id ])) return false;
        return $this->shares[ $source->id ];
    }

    function _setSortRating( $source, $direction ) {
        $ratings = $this->ratings;
        $ordered_ratings = ( $this->_sort_direction == AMP_SORT_DESC ) ? arsort( $ratings ) : $ratings;
        $this->_source = array_combine_key( $ordered_ratings, $source );
    }

}


?>
