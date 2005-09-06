<?php

require_once ('AMP/Content/Section/Contents/Subsections.inc.php' );

class SectionContentSource_SectionsBySubsection extends SectionContentSource_Subsections {

    function SectionContentSource_SectionsBySubsection( &$section ) {
        $this->init( $section );
    }

    function getSectionCriteria() {
        $map = &AMPContent_Map::instance();
        if (!($subsection_set = $map->getChildren( $this->_section->id ))) return 'false';
        return "parent in (" . join( ", ", $subsection_set ) . ")";
    }
}
?>
