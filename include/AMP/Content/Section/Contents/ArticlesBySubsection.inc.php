<?php

require_once ('AMP/Content/Section/Contents/Articles.inc.php' );

class SectionContentSource_ArticlesBySubsection extends SectionContentSource_Articles {

    function SectionContentSource_ArticlesBySubsection( &$section ) {
        $this->init( $section );
    }

    function _addCriteriaSection( ){
        $article = new Article(  AMP_Registry::getDbcon( ));
        $raw_crit = $this->_section->getDisplayCriteria( );
        if( isset( $raw_crit['section'] ) && $raw_crit['section'] ) {
            $raw_crit['in_section_descendant'] = $raw_crit['section'];
            unset( $raw_crit['section']);
        }
        $crit = $article->makeCriteria( $raw_crit ); 
        foreach( $crit as $crit_item ) {
            $this->_source->addCriteria( $crit_item );
        }
        #$this->_source->addCriteriaSectionDescendentRelational( $this->_section->id );
    }

    function getSectionCriteria() {
        //deprecated, use above instead
        $map = &AMPContent_Map::instance();
        if (!($subsection_set = $map->getChildren( $this->_section->id ))) return 'false';
        return "type in (" . join( ", ", $subsection_set ) . ")";
    }

}
?>
