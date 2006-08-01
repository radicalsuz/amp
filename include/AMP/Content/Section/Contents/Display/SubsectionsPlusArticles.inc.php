<?php

require_once ('AMP/Content/Section/Contents/Subsections.inc.php');

class SectionContentDisplay_SubsectionsPlusArticles extends ArticleSet_Display {

    var $_subsections_display;

    function SectionContentDisplay_SubsectionsPlusArticles ( &$articleSet, $read_data = true ) {
        $this->init( $articleSet, $read_data );
    }

    function setSection( &$section ) {
        $subsections_source = &new SectionContentSource_Subsections( $section );
        $subsections_set = $subsections_source->execute();
        $subsections_set->readData();
        $this->_subsections_display = &new SectionSet_Display( $subsections_set );
    }

    function execute() {
        $output = "";
        if (isset($this->_subsections_display)) $output = $this->_subsections_display->execute() ;
        return $output . parent::execute();
    }

}
?>
