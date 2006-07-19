<?php

require_once( 'AMP/Content/Article.inc.php' );

class Article_Version extends Article {

    var $datatable = "articles_version";
    var $id_field = "vid";
    var $_class_name = 'Article_Version';
    var $_sort_auto = true;
    var $_allow_db_cache = false;

    function Article_Version ( &$dbcon, $id = null ) {
        $this->init( $dbcon, $id );
    }

    function makeCriteriaArticle( $article_id ){
        return 'id=' . $article_id;
    }

    function getArticleId( ){
        return $this->getData( 'id' );
    }

    function restore( ){
        $article = & new Article( $this->dbcon, $this->getArticleId( ) );
        $article->saveVersion( );
        $article->readVersion( $this->id );
        return $article->save( );
    }

    function _sort_default( &$item_set ){
        return $this->sort( $item_set, 'itemDateChanged', AMP_SORT_DESC );
    }

    function getURL() {
        if ($url = $this->getRedirect() ) return $url;
        if (!$this->id ) return false;
        return AMP_Url_AddVars( AMP_CONTENT_URL_ARTICLE, array( "vid=".$this->id, 'id='.$this->getArticleId( ),'preview=1' ));
    }

}
?>
