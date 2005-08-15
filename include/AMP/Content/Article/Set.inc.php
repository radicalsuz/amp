<?php

require_once ('AMP/System/Data/Set.inc.php');
require_once ('AMP/Content/Article.inc.php');

class ArticleSet extends AMPSystem_Data_Set {

    var $datatable = "articles";
    var $_articles;

    function ArticleSet ( &$dbcon ) {
        $this->init ( $dbcon );
    }

    function getArticles() {
        if (isset($this->_articles)) return $this->_articles;
        return $this->instantiateItems( $this->getArray(), 'Article' );
    }

}
?>
