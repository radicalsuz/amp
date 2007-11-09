<?php

require_once ('AMP/Content/Section/Contents/Subsections.inc.php' );

class SectionContentSource_SectionsBySubsection extends SectionContentSource_Subsections {

    function SectionContentSource_SectionsBySubsection( &$section ) {
        $this->init( $section );
    }

    function _addCriteriaSection( ){
        $raw_crit = $this->_section->getDisplayCriteria( );
        if( isset( $raw_crit['section']) && $raw_crit['section'] ) {
            $raw_crit['grandparent'] = $raw_crit['section'];
            unset( $raw_crit['section']);
        }
        $crit = $this->_section->makeCriteria( $this->_section->getDisplayCriteria( ));
        foreach( $crit as $crit_item ) {
            $this->_source->addCriteria( $crit_item );
        }
        #$this->_source->addCriteriaSectionParent( $this->_section->id );
    }

    function getSectionCriteria() {
        //deprecated, use above instead
        $map = &AMPContent_Map::instance();
        if (!($subsection_set = $map->getChildren( $this->_section->id ))) return 'false';
        return "parent in (" . join( ", ", $subsection_set ) . ")";
    }
}
?>
