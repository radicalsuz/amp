<?php

if (!defined( 'AMP_CONTENT_LIST_SUBHEADER_CLASS' )) define( 'AMP_CONTENT_LIST_SUBHEADER_CLASS', 'title' );

class SectionContentDisplay_ArticlesBySubsection extends ArticleSet_Display {

    var $_subsections_display;
    var $_css_class_subheader = AMP_CONTENT_LIST_SUBHEADER_CLASS;
    var $_pager_active = false;
    var $_pager_limit = 20;
    var $_source_section;

    function SectionContentDisplay_ArticlesBySubsection ( &$articleSet, $read_data = true ) {
        $this->init( $articleSet, $read_data );
    }

    function execute() {
        if (!$this->_source->makeReady()) return false;
        $subsections = $this->_subsections_display->_buildItems( $this->_subsections_display->getSourceArray() );
        $listBody = "";
        if ( !( $page_limit = $this->_source_section->getListItemLimit( ))) $page_limit = $this->_pager_limit;

        foreach ($subsections as $subsection ) {
            if( !($article_data = &$this->_source->filter( 'type', $subsection->id , $this->_pager_limit ))) continue;
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
        $this->_source_section = &$section;
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
