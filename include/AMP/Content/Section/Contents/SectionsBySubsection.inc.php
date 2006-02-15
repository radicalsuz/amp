<?php

require_once ('AMP/Content/Section/Contents/Subsections.inc.php' );

class SectionContentSource_SectionsBySubsection extends SectionContentSource_Subsections {

    function SectionContentSource_SectionsBySubsection( &$section ) {
        $this->init( $section );
    }

    function _addCriteriaSection( ){
        $this->_source->addCriteriaSectionParent( $this->_section->id );
    }

    function getSectionCriteria() {
        //deprecated, use above instead
        $map = &AMPContent_Map::instance();
        if (!($subsection_set = $map->getChildren( $this->_section->id ))) return 'false';
        return "parent in (" . join( ", ", $subsection_set ) . ")";
    }
}
?>
