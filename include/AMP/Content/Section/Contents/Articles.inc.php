<?php
require_once( 'AMP/Content/Section/Contents/Source.inc.php' );

class SectionContentSource_Articles extends SectionContentSource {

    var $_base_sort = array(
        "date DESC", 
        "id DESC "
        ) ;

    function SectionContentSource_Articles( &$section ) {
        $this->init( $section );
    }


    ###################################
    ### private data source methods ###
    ###################################

    function _setSource() {
        $this->_source = &new ArticleSet( $this->_section->dbcon );
    }

    function _setBaseCriteria() {
        $this->_display_crit_source->clean( $this->_source );
    }

    function _setCriteria() {
        $this->_setBaseCriteria();
        $this->_source->addCriteria( $this->getSectionCriteria() );
        $this->_addLegacyCriteria() ;
    }

    function getSectionCriteria() { 
        $base_section = "type=".$this->_section->id ;
        if (!AMP_ARTICLE_ALLOW_MULTIPLE_SECTIONS) return $base_section;
        if (!($related_ids = $this->_getRelatedArticles())) return $base_section;

        return "( ". $base_section . ' OR ' . $related_ids . ")";
    }

    function _setSort() {
        $this->_setBaseSort();
        $this->_source->addSort(
        "if(isnull(pageorder) or pageorder='', ". AMP_CONTENT_LISTORDER_MAX .", pageorder) ASC");
    }

    function _getRelatedArticles( $section_id = null) {
        require_once( 'AMP/Content/Section/RelatedSet.inc.php' );
        if (!isset($section_id)) $section_id = $this->_section->id;

        $related = &new SectionRelatedSet( $this->_section->dbcon, $section_id );
        $relatedContent = &$related->getLookup( 'typeid' );
        if (empty( $relatedContent )) return false;

        return "id in (" . join( ", ", array_keys( $relatedContent) ). ")";
    }

    function _addLegacyCriteria() {
        require_once( 'AMP/Content/Article/LegacySearch.inc.php' );
        $search = &new Article_LegacySearch( $this->_section->dbcon );
        if (!($legacy_set = $search->getCriteria() )) return false;
        foreach ($legacy_set as $legacy_item ) {
            $this->_source->addCriteria( $legacy_item );
        }
        return true;
    }

}
?>
