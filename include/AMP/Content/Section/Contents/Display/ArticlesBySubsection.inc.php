<?php

class SectionContentDisplay_ArticlesBySubsection extends ArticleSet_Display {

    var $_subsections_display;
    var $_css_class_subheader = AMP_CONTENT_LIST_SUBHEADER_CLASS;
    var $_css_class_morelink= "go";
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
            if( !($article_data = $this->_getArticleData( $subsection->id , $page_limit ))) continue;
            #if( !($article_data = &$this->_source->filter( 'type', $subsection->id , $page_limit ))) continue;
            $articles  = $this->_buildItems( $article_data );
            $listBody .= $this->_HTML_subheader( $subsection )
                         . $this->_HTML_listing( $articles )
                         . $this->_checkMoreLink( $subsection, $page_limit );
        }
        return $listBody;

    }

    function &_getArticleData( $section_id, $max_qty ){
        $empty_value = false;
        if (!$this->_source->makeReady()) return $empty_value;
        $result = array();
        $related_set = AMPContentLookup_RelatedArticles::instance( $section_id );
        $related_ids = $related_set ? array_keys( $related_set) : array( );

        while( $data = $this->_source->getData() ) {
            if (isset($max_qty) && count($result)==$max_qty) break;
            if ( ( $data[ 'type' ] != $section_id ) && ( array_search( $data['id'], $related_ids ) === FALSE )) continue;
            $result[ $data['id'] ] = $data;
        }

        if (empty($result)) return $empty_value;
        return $result;

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

    function _checkMoreLink( $section, $limit ) {
        if ( !isset( $this->_subsection_counts )) $this->_subsection_counts = $this->_source->getGroupedIndex( 'type' ) ;
        if ( !( isset( $this->_subsection_counts[ $section->id ]) && $this->_subsection_counts[ $section->id ] > $limit )) return false; 
        return $this->_HTML_moreLink( $this->_getMoreLinkHref( $section->id, $limit ), $section->getName( ));
        
    }

    function _HTML_moreLink( $href, $name=false ) {

        $text = 'More&nbsp;' . $name . '&nbsp;' . $this->_HTML_bold( '&raquo;' );
        return $this->_HTML_inDiv( 
                    $this->_HTML_inSpan( $this->_HTML_link( $href, $text ), $this->_css_class_morelink ),
                    array( 'class'=>'list_pager' )
                    );
    }
    function _getMoreLinkHref( $section_id, $limit ) {
        return  
            AMP_URL_AddVars( 
                AMP_CONTENT_URL_LIST_SECTION, 
                array(  "type=$section_id",
                        "offset=".$limit ) 
                );
    }

}
?>
