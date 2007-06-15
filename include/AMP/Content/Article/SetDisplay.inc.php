<?php

require_once( 'AMP/Content/Article/Set.inc.php' );
require_once( 'AMP/Content/Display/List.inc.php' );

//this is a special version of Article display
//i changed the cSS and added the subtitle - TED


class ArticleSet_Display extends AMPContent_DisplayList_HTML {
    var $_css_class_author = "bodygreystrong";
    var $_css_class_source = "bodygreystrong";

    var $_css_class_title    = "listtitle";
    var $_css_class_subtitle = "subtitle";
    var $_css_class_morelink = "go";
    var $_css_class_text     = "text";
    var $_css_class_date     = "fpdetails";

    function ArticleSet_Display( &$articleSet, $read_data = true ) {
        $this->init( $articleSet, $read_data );
    }

    function _HTML_listItemDescription( &$article ) {
        return
            $this->_HTML_listItemTitle( $article ) . 
            $this->_HTML_listItemSource( $article->getAuthor(), $article->getSource(), $article->getSourceURL()) .
            $this->_HTML_listItemDate( $article->getItemDate() ) .
            $this->_HTML_listItemBlurb( $article->getBlurb() );
    }

    function render_subtitle( $source ) {
        $renderer = AMP_get_renderer( );
        return $renderer->span( $source->getSubtitle( ), array( 'class' => $this>_css_class_subtitle ));
    }


    function _HTML_listItemSource( $author, $source, $url ) {
        if (!(trim($author) || $source || $url)) return false;
        $output_author = FALSE;
        $output_source = FALSE;

        if (trim($author)) {
            $output_author =  $this->_HTML_inSpan( 'by&nbsp;' . converttext($author), $this->_css_class_author );
            if (!$source) return $output_author . $this->_HTML_newline();
        }

        if ($source) $output_source = $this->_HTML_inSpan( $this->_HTML_link( $url, $source  ), $this->_css_class_source );

        if ($output_author && $output_source) return $output_author . ',&nbsp;' . $output_source . $this->_HTML_newline();

        return $output_source . $this->_HTML_newline();
    }

}
?>
