<?php

require_once ('AMP/System/Data/Set.inc.php');
require_once ('AMP/Content/Article.inc.php');

class ArticleSet extends AMPSystem_Data_Set {

    var $datatable = "articles";
    var $_articles;
    var $_search_class = "ContentSearch";

    function ArticleSet ( &$dbcon ) {
        $this->init ( $dbcon );
    }

    function getArticles() {
        if (isset($this->_articles)) return $this->_articles;
        return $this->instantiateItems( $this->getArray(), 'Article' );
    }

    function applySearch( $search_values, $run_query=true ) {
        require_once( 'AMP/Content/Search.inc.php');
        $search = &$this->getSearch( );
        $search->applyValues( $search_values );
        if ( $run_query ) $this->readData( );
    }

    function addCriteriaSection( $value ) {
        $base_section = "type=" . $value;
        if (!AMP_ARTICLE_ALLOW_MULTIPLE_SECTIONS) return $this->addCriteria( $base_section );
        require_once( 'AMP/Content/Section/Contents/Manager.inc.php');
        if (!($related_ids = SectionContents_Manager::getRelatedArticles( $value ))) return $this->addCriteria( $base_section );

        return $this->addCriteria( "( ". $base_section . ' OR ' . $related_ids . ")" );
    }

}
?>
