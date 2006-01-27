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

    function addCriteriaStatus( $value ) {
        if ( !( $value || $value==='0')) return false;
        $this->addCriteria( 'publish='.$value ) ;
    }
    
    function addCriteriaNew() {
        $this->addCriteria( 'new= 1 ' ) ;
    }

    function addCriteriaFp() {
        $this->addCriteria( 'fplink= 1 ' ) ;
    }

    function addFilter( $filter_name ) {
        $filter_path = 'AMP/Content/Article/Filter/'. ucfirst( $filter_name) . '.inc.php';
        if ( !file_exists_incpath( $filter_path )) return false;
        include_once( $filter_path );
        $filter_class = 'ContentFilter_' . ucfirst( $filter_name );
        $sourceFilter = &new $filter_class();
        return $sourceFilter->execute( $this );
    }

    function addCriteriaClass( $class_value ) {
        if ( !$class_value ) return false;
        if ( is_array( $class_value ) && !empty( $class_value )) return $this->addCriteria( 'class in ( ' . join( ',', $class_value ) . ' )');
        return $this->addCriteria( 'class=' . $class_value ) ;
    }

    function addCriteriaExcludeClass( $class_value ){
        if ( !$class_value ) return false;
        if ( is_array( $class_value ) && !empty( $class_value )) return $this->addCriteria( 'class not in ( ' . join( ',', $class_value ) . ' )');
        return $this->addCriteria( 'class!=' . $class_value ) ;

    }

    function addSortNewestFirst( ) {
        $this->setSort( array( 'date DESC', 'id DESC'));
    }

}
?>
