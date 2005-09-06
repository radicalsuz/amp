<?php

require_once ( 'AMP/Content/Section/SetDisplay.inc.php' );

class SectionContentDisplay_SectionsBySubsection extends SectionSet_Display {

    var $_subsections_display;
    var $_css_class_subheader = AMP_CONTENT_LIST_SUBHEADER_CLASS;

    function SectionContentDisplay_SectionsBySubSection( &$sectionSet ) {
        $this->init( $sectionSet );
    }

    function execute() {
        if (!$this->_source->makeReady()) return false;
        $subsections = $this->_subsections_display->_buildItems( $this->_subsections_display->getSourceArray() );
        $listBody = "";

        foreach ($subsections as $subsection ) {
            if( !($section_data = &$this->_source->filter( 'parent', $subsection->id ))) continue;
            $sections = &$this->_buildItems( $section_data );
            $listBody .= $this->_HTML_subheader( $subsection ).
                         $this->_HTML_listing( $sections )  ;
        }
        return $listBody;

    }

    function setSection( &$section ) {
        $subsections_source = &new SectionContentSource_Subsections( $section );
        $subsections_set = $subsections_source->execute();
        $subsections_set->readData();
        $this->_subsections_display = &new SectionSet_Display( $subsections_set );
    }


    function _HTML_subheader( &$section ) {
        $blurb = $section->getBlurb();
        return 
            $this->_HTML_subheaderTitle( $section->getName() ) .
            $this->_HTML_listItemBlurb( $blurb ) .
            $this->_HTML_newline( ( $blurb ? 2 : 1 ) );
    }

    function _HTML_subheaderTitle( $title ) {
        return $this->_HTML_in_P( $title, array( 'class' => $this->_css_class_subheader ) );
    }

}
?>
