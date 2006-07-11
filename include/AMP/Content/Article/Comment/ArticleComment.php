<?php

require_once( 'AMP/System/Data/Item.inc.php');

class ArticleComment extends AMPSystem_Data_Item {

    var $datatable = "comments";
    var $name_field = "comment";
    var $_sort_auto = false;
    var $_class_name = 'ArticleComment';

    function ArticleComment ( &$dbcon, $id = null ) {
        $this->init( $dbcon, $id );
    }

    function getTimestamp( ){
        if ( !$result = $this->getData( 'date' )) return null;
        return strtotime( $result );
    }

    function getDate( ){
        return $this->getData( 'date' );
    }

    function _sort_default( &$item_set ){
        return $this->sort( $item_set, 'timestamp', AMP_SORT_DESC );
    }


    function makeCriteriaArticle( $article_id ){
        return $this->_makeCriteriaEquals( 'articleid', $article_id );
    }
    function makeCriteriaArticle_id( $article_id ){
        return $this->makeCriteriaArticle( $article_id );
    }
    function makeCriteriaCid( $article_id ){
        return $this->makeCriteriaArticle( $article_id );
    }

    function getAuthor( ){
        return $this->getData( 'author' );
    }

    function setDefaults( ) {
        $this->mergeData( array( 
            'author_IP' => $_SERVER['REMOTE_ADDR'],
            'agent'     => $_SERVER['HTTP_USER_AGENT'],
            'publish'   => AMP_CONTENT_STATUS_LIVE,
            'user_id'   => ( defined( 'AMP_SYSTEM_USER_ID') ? AMP_SYSTEM_USER_ID : false ),
            'date'      => date( 'Y-m-d H:i:s' )
        ));
    }
}

?>
