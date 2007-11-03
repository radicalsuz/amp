<?php

require_once( 'AMP/Display/System/List.php' );
require_once( 'Modules/Rating/Rating.php' );
require_once( 'AMP/Content/Article.inc.php');

class Rating_List extends AMP_Display_System_List {

    var $columns = array( 'controls', 'name', 'rating' );
    var $column_headers = array( 'name' => 'Article', 'rating' => 'Avg Rating' );
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
    }

    function render_rating( $source ) {
        return sprintf( '%01.2f', $this->ratings[ $source->id ]);
    }

    function _setSortRating( $source, $direction ) {
        $ratings = $this->ratings;
        $ordered_ratings = ( $this->_sort_direction == AMP_SORT_DESC ) ? arsort( $ratings ) : $ratings;
        $this->_source = array_combine_key( $ordered_ratings, $source );
    }

}


?>
