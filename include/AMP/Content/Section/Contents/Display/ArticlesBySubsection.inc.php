<?php

class SectionContentDisplay_ArticlesBySubsection extends ArticleSet_Display {

    var $_subsections_display;
    var $_css_class_title = "title";

    function SectionContentDisplay_ArticlesBySubsection ( &$articleSet ) {
        $this->init( $articleSet );
    }

    function execute() {
        if (!$this->_source->makeReady()) return false;
        $subsections = $this->_subsections_display->_buildItems( $this->_subsections_display->getSourceArray() );
        $listBody = "";

        foreach ($subsections as $subsection ) {
            if( !($article_data = &$this->_source->filter( 'type', $subsection->id , $this->_pager->getLimit() ))) continue;
            $articles  = &$this->_buildItems( $article_data );
            $listBody .= $this->_HTML_subheader( $subsection ).
                         $this->_HTML_listing( $articles )  ;
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
        return 
        $this->_HTML_subheaderTitle( $section->getName() ) .
        $this->_HTML_listItemBlurb( $section->getBlurb() ) .
        $this->_HTML_newline(2) ;
    }

    function _HTML_subheaderTitle( $title ) {
        return $this->_HTML_in_P( $title, array( 'class' => $this->_css_class_title ) );
    }

}
?>
