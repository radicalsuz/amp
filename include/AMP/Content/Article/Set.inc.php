<?php

require_once ('AMP/System/Data/Set.inc.php');
require_once ('AMP/Content/Article.inc.php');

class ArticleSet extends AMPSystem_Data_Set {

    var $datatable = "articles";
    var $_articles;
    var $_search_class = "ContentSearch";
    var $_item_type = AMP_SYSTEM_ITEM_TYPE_ARTICLE;

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
        return $this->addCriteria( $this->_getCriteriaSection( $value ));
    }

    function _getCriteriaSection( $section_id ){
        $base_section = $this->makeCriteriaSectionBase( $section_id );
        if ( !( $related_ids = $this->_makeCriteriaSectionRelated( $section_id ))) {
            return $base_section;
        }
        return "( ". $base_section . ' OR ' . $related_ids . ")" ;
    }

    function makeCriteriaSectionBase( $section_id ) {
        return "type=" . $section_id;
    }

    function _makeCriteriaSectionRelated( $section_id ) {
        if (!AMP_ARTICLE_ALLOW_MULTIPLE_SECTIONS) return false;
        require_once( 'AMP/Content/Section/Contents/Manager.inc.php');
        return SectionContents_Manager::getRelatedArticles( $section_id );
    }

    function makeCriteriaDate( $date_value ) {
        require_once( 'AMP/Content/Article.inc.php' );
        $sample_article = new Article( $this->dbcon );
        return $sample_article->makeCriteriaDate( $date_value );
    }


    function addCriteriaPublic( ){
        $protected_sections = AMPContent_Lookup::instance( 'protectedSections');
        if ( empty( $protected_sections )) return;
        $this->addCriteria( 'type not in( '. join( ',', array_keys( $protected_sections) ) .' )');

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

    function addFilter( $filter_name, $filter_var = null ) {
        $filter_class = 'ContentFilter_' . ucfirst( $filter_name );
        if ( class_exists( $filter_class )) {
            $sourceFilter = &new $filter_class( $filter_var );
            return $sourceFilter->execute( $this );
        }

        $filter_filename = ucfirst( $filter_name ) . '.inc.php';
        $filter_path = 'AMP/Content/Article/Filter/'. $filter_filename;
        if ( !file_exists_incpath( $filter_path )) {
            if ( !( $filter_path = file_exists_incpath( $filter_filename ))) {
                return false;
            }
        }
        include_once( $filter_path );
        $sourceFilter = &new $filter_class( $filter_var );
        return $sourceFilter->execute( $this );
    }

    function addCriteriaClass( $class_value ) {
        if ( !$class_value ) return false;
        return $this->addCriteria( $this->_getCriteriaClass( $class_value ));
    }

    function _getCriteriaClass( $class_value ){
        if ( is_array( $class_value ) && !empty( $class_value )) return 'class in ( ' . join( ',', $class_value ) . ' )';
        return 'class=' . $class_value  ;
    }

    function addCriteriaExcludeClass( $class_value ){
        if ( !$class_value ) return false;
        if ( is_array( $class_value ) && !empty( $class_value )) return $this->addCriteria( 'class not in ( ' . join( ',', $class_value ) . ' )');
        return $this->addCriteria( 'class!=' . $class_value ) ;

    }

    function addCriteriaSectionDescendent( $section_id ){
        $base_section = "type=".$section_id ;
        $map = &AMPContent_Map::instance( );

        if (!($child_ids = $map->getDescendants( $section_id ))) return $this->addCriteria( $base_section );
        $child_sections = 'type in ( ' . join(',', $child_ids ).' )';
        $this->addCriteria(  "(" . $child_sections . ' OR '. $base_section . ")");

    }

    function addCriteriaSectionDescendentRelational( $section_id ){
        $base_section = $this->_getCriteriaSection( $section_id );
        $map = &AMPContent_Map::instance( );

        if (!($child_ids = $map->getDescendants( $section_id ))) return $this->addCriteria( $base_section );
        foreach( $child_ids as $child_id ){
            $child_sections[] = $this->_getCriteriaSection( $child_id );
        }
        $child_sections_criteria = '( '. join( ') OR ( ', $child_sections ) . ')';
        $this->addCriteria(  "(" . $child_sections_criteria . ' OR '. $base_section . ")");

    }

    function addSortNewestFirst( ) {
        $this->setSort( array( 'date DESC', 'id DESC'));
    }

    function addCriteriaSectionOrClass( $section_id, $class_id ){
        $section_criteria = $this->_getCriteriaSection( $section_id );
        $class_criteria = $this->_getCriteriaClass( $class_id );
        return $this->addCriteria( "( ". $class_criteria . " OR " . $section_criteria ." )");
    }

}
?>
