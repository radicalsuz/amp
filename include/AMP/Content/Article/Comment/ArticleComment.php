<?php

require_once( 'AMP/System/Data/Item.inc.php');

class ArticleComment extends AMPSystem_Data_Item {

    var $datatable = "comments";
    var $name_field = "comment";

    function ArticleComment ( &$dbcon, $id = null ) {
        $this->init( $dbcon, $id );
    }

    function getTimestamp( ){
        return strtotime( $this->getData( 'date'));
    }

    function getDate( ){
        return $this->getData( 'date');
    }

    function _sort_default( &$item_set ){
        return $this->sort( $item_set, 'timestamp', AMP_SORT_DESC );
    }

    function isLive() {
        return ($this->getData('publish')==AMP_CONTENT_STATUS_LIVE);
    }

    function getPublish( ){
        return $this->isLive( ) ;

    }

    function publish( ){
        if ( $this->isLive( )) return false;
        $this->mergeData( array( 'publish' => AMP_CONTENT_STATUS_LIVE ));
        if ( !( $result = $this->save( ))) return false;
        $this->notify( 'update');
        $this->notify( 'publish');
        return $result;
    }
    function unpublish( ){
        if ( !$this->isLive( )) return false;
        $this->mergeData( array( 'publish' => AMP_CONTENT_STATUS_DRAFT ));
        if ( !( $result = $this->save( ))) return false;
        $this->notify( 'update');
        $this->notify( 'unpublish');
        return $result;
    }

    function makeCriteriaArticle( $article_id ){
        return $this->_makeCriteriaEquals( 'articleid', $article_id );
    }
    function makeCriteriaCid( $article_id ){
        return $this->makeCriteriaArticle( $article_id );
    }
}

?>
