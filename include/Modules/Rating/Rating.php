<?php
require_once( 'AMP/System/Data/Item.inc.php');

class AMPSystemLookup_ArticleRatings extends AMPSystem_Lookup{
    var $datatable = 'ratings';
    var $id_field = 'item_id';
    var $result_field = 'average( rating )';
    var $criteria = 'item_type="article" GROUP BY item_id';

    function AMPSystemLookup_ArticleRatings( ) {
        $this->init( );
    }
}

class AMPSystemLookup_ArticleRatingsBySession extends AMPSystem_Lookup{
    var $datatable = 'ratings';
    var $id_field = 'item_id';
    var $result_field = 'rating';
    var $criteria = 'item_type="article"';
    var $_base_criteria = 'item_type="article"';

    function AMPSystemLookup_ArticleRatingsBySession( $session ) {
        if ( $session ) {
            $this->_filter_by_session( $session );
        }

        $this->init( );
    }

    function _filter_by_session( $session ) {
        $dbcon = AMP_Registry::getDbcon( );
        $this->criteria = $this->_base_criteria . ' AND session=' . $dbcon->qstr( $session );
    }
}

class AMPSystemLookup_ArticleIdsRatedBySession extends AMPSystemLookup_ArticleRatingsBySession{
    var $result_field = 'id';

    function AMPSystemLookup_ArticleIdsRatedBySession( $session ) {
        if ( $session ) {
            $this->_filter_by_session( $session );
        }

        $this->init( );
    }
}

class Rating extends AMPSystem_Data_Item {
    var $datatable = 'ratings';
    var $data = array( );

    function Rating( $dbcon, $id = null) {
        $this->__construct( $dbcon, $id );
    }


}

class ArticleRating {

    function __construct( $article_id, $rating ) {

    }

    function current( $item_id ) {
        if ( !AMP_SYSTEM_UNIQUE_VISITOR_ID ) return false;
        $session = AMP_SYSTEM_UNIQUE_VISITOR_ID;
        $current_ratings = AMP_lookup( 'article_ratings_by_session', $session );
        if( !isset( $current_ratings[$item_id])) return false;
        return $current_ratings[ $item_id ];
    }

    function create( $item_id, $rating_value ) {
        if ( !AMP_SYSTEM_UNIQUE_VISITOR_ID ) return false;
        $session = AMP_SYSTEM_UNIQUE_VISITOR_ID;
        $articles_rated = AMP_lookup( 'article_ids_rated_by_session', $session );
        if( isset( $articles_rated[$item_id])) return ArticleRating::update( $item_id, $rating_value );

        $rating = new Rating( AMP_Registry::getDbcon( ));
        $rating_data = array( 'session' => $session, 'item_type' => 'article', 'item_id' => $item_id, 'rating' => $rating_value );
        $rating->mergeData( $rating_data );
        $result = $rating->save( );
        AMP_lookup_clear_cached( 'article_ids_rated_by_session', $session );
        AMP_lookup_clear_cached( 'article_ratings_by_session', $session );
        return $result;

    }

    function update( $item_id, $rating_value ) {
        if ( !AMP_SYSTEM_UNIQUE_VISITOR_ID ) return false;
        $session = AMP_SYSTEM_UNIQUE_VISITOR_ID;
        $articles_rated = AMP_lookup( 'article_ids_rated_by_session', $session );
        if( !isset( $articles_rated[ $item_id ])) return ArticleRating::create( $item_id, $rating_value );
        $rating_id = $articles_rated[ $item_id ];

        $rating = new Rating( AMP_Registry::getDbcon( ), $rating_id );
        if( !$rating->hasData( )) return false;
        $rating->mergeData( array( 'rating' => $rating_value ));
        $result = $rating->save( );
        AMP_lookup_clear_cached( 'article_ids_rated_by_session', $session );
        AMP_lookup_clear_cached( 'article_ratings_by_session', $session );
        return $result;

    }

}




?>
