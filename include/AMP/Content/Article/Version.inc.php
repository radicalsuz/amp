<?php

require_once( 'AMP/Content/Article.inc.php' );

class Article_Version extends Article {

    var $datatable = "articles_version";
    var $id_field = "vid";
    var $_class_name = 'Article_Version';

    function Article_Version ( &$dbcon, $id = null ) {
        $this->init( $dbcon, $id );
    }

    function makeCriteriaArticle( $article_id ){
        return 'id=' . $article_id;
    }

    function getArticleId( ){
        return $this->getData( 'id' );
    }

}
?>
