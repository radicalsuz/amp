<?php
require_once( 'AMP/System/Data/Item.inc.php');

class AMPSystemLookup_ArticleRatings extends AMPSystem_Lookup{
    var $datatable = 'ratings';
    var $id_field = 'item_id';
    var $result_field = 'avg( rating ) as rank';
    var $criteria = 'item_type="article" GROUP BY item_id';
    var $_base_criteria = 'item_type="article" GROUP BY item_id';
    var $sortby = 'rank desc';

    function AMPSystemLookup_ArticleRatings( ) {
        $this->init( );
    }
}

class AMPSystemLookup_ArticleRatingsLastWeek extends AMPSystemLookup_ArticleRatings{

    function AMPSystemLookup_ArticleRatingsLastWeek( ) {
        $this->filter_by_week( );
        $this->init( );
    }

    function filter_by_week( ) {
        $this->criteria = 'updated_at > "'.date( 'Y-m-d h:i:s', time( ) - ( 60*60*24*7)) . '" and '
                            . $this->_base_criteria;
    }
}

class AMPSystemLookup_ArticleRatingsLastMonth extends AMPSystemLookup_ArticleRatings{

    function AMPSystemLookup_ArticleRatingsLastMonth( ) {
        $this->filter_by_month( );
        $this->init( );
    }

    function filter_by_month( ) {
        $this->criteria = 'updated_at > "'.date( 'Y-m-d h:i:s', time( ) - ( 60*60*24*30)) . '" and '
                            . $this->_base_criteria;
    }
}

class AMPSystemLookup_ArticleRatingsBySection extends AMPSystem_Lookup{
    var $datatable = 'ratings a, articles b, articlereltype c';
    var $id_field = 'a.item_id';
    var $result_field = 'avg( a.rating ) as rank';
    var $criteria = 'a.item_type="article" AND ( a.item_id = b.id OR a.item_id = c.articleid ) GROUP BY a.item_id';
    var $_base_criteria = 'a.item_type="article" AND ( a.item_id = b.id OR a.item_id = c.articleid ) GROUP BY a.item_id';
    var $sortby = 'rank desc, b.date desc';

    function AMPSystemLookup_ArticleRatingsBySection( $section_id ) {
        if ( $section_id ) {
            $this->_filter_by_section( $section_id );
        }

        $this->init( );
    }

    function _filter_by_section_id ( $section_id ) {
        $this->criteria = '( b.type = ' . $section_id . ' OR c.typeid = ' . $section_id . ' ) AND ' .$this->_base_criteria ;
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

    function average( $item_id ) {
        $current_ratings = AMP_lookup( 'article_ratings' );
        if( !isset( $current_ratings[$item_id])) return false;
        return $current_ratings[ $item_id ];
    }

    function create( $item_id, $rating_value ) {
        if ( !AMP_SYSTEM_UNIQUE_VISITOR_ID ) return false;
        $session = AMP_SYSTEM_UNIQUE_VISITOR_ID;
        $articles_rated = AMP_lookup( 'article_ids_rated_by_session', $session );
        if( isset( $articles_rated[$item_id])) return ArticleRating::update( $item_id, $rating_value );

        $rating = new Rating( AMP_Registry::getDbcon( ));
        $rating_data = array( 'session' => $session, 'item_type' => 'article', 'item_id' => $item_id, 'rating' => $rating_value, 'updated_at' => date('Y-m-d h:i:s' ) );
        $rating->mergeData( $rating_data );
        $result = $rating->save( );
        AMP_lookup_clear_cached( 'article_ids_rated_by_session', $session );
        AMP_lookup_clear_cached( 'article_ratings_by_session', $session );
        AMP_lookup_clear_cached( 'article_ratings' );
        AMP_lookup_clear_cached( 'article_ratings_last_week' );
        AMP_lookup_clear_cached( 'article_ratings_last_month' );
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
        $rating->mergeData( array( 'rating' => $rating_value , 'updated_at' => date( 'Y-m-d h:i:s')));
        $result = $rating->save( );
        AMP_lookup_clear_cached( 'article_ids_rated_by_session', $session );
        AMP_lookup_clear_cached( 'article_ratings_by_session', $session );
        AMP_lookup_clear_cached( 'article_ratings' );
        AMP_lookup_clear_cached( 'article_ratings_last_week' );
        AMP_lookup_clear_cached( 'article_ratings_last_month' );
        return $result;

    }

}




?>
