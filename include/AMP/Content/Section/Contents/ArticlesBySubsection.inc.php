<?php

require_once ('AMP/Content/Section/Contents/Articles.inc.php' );

class SectionContentSource_ArticlesBySubsection extends SectionContentSource_Articles {

    function SectionContentSource_ArticlesBySubsection( &$section ) {
        $this->init( $section );
    }

    function getSectionCriteria() {
        $map = &AMPContent_Map::instance();
        if (!($subsection_set = $map->getChildren( $this->_section->id ))) return 'false';
        return "type in (" . join( ", ", $subsection_set ) . ")";
    }
}
?>
